<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "invoices".
 *
 * @property int $id
 * @property string $code
 * @property int $quotation_id
 * @property int $project_id
 * @property int $customer_id
 * @property string $date
 * @property string $due_date
 * @property string $subject
 * @property string $notes
 * @property float $sub_total
 * @property float $discount_total
 * @property float $vat_rate
 * @property float $vat_amount
 * @property float $wht_rate
 * @property float $wht_amount
 * @property float $grand_total
 * @property float $paid_amount
 * @property float $balance
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Quotation $quotation
 * @property Project $project
 * @property Customer $customer
 * @property InvoiceItem[] $items
 * @property Receipt[] $receipts
 */
class Invoice extends \yii\db\ActiveRecord
{
    const STATUS_DRAFT = 0;
    const STATUS_SENT = 1;
    const STATUS_PARTIAL = 2;
    const STATUS_PAID = 3;
    const STATUS_OVERDUE = 4;
    const STATUS_CANCELLED = 5;

    public static function tableName()
    {
        return 'invoices';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['code', 'project_id', 'customer_id', 'date'], 'required'],
            [['quotation_id', 'project_id', 'customer_id', 'status'], 'integer'],
            [['date', 'due_date', 'created_at', 'updated_at'], 'safe'],
            [['sub_total', 'discount_total', 'vat_rate', 'vat_amount', 'wht_rate', 'wht_amount', 'grand_total', 'paid_amount', 'balance'], 'number'],
            [['code'], 'string', 'max' => 32],
            [['subject', 'notes'], 'string'],
            [['code'], 'unique'],
            [['quotation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Quotation::class, 'targetAttribute' => ['quotation_id' => 'id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['project_id' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'เลขที่ใบแจ้งหนี้',
            'quotation_id' => 'ใบเสนอราคา',
            'project_id' => 'โครงการ',
            'customer_id' => 'ลูกค้า',
            'date' => 'วันที่',
            'due_date' => 'วันครบกำหนด',
            'subject' => 'เรื่อง',
            'notes' => 'หมายเหตุ',
            'sub_total' => 'รวมก่อนลด',
            'discount_total' => 'ส่วนลด',
            'vat_rate' => 'VAT (%)',
            'vat_amount' => 'VAT',
            'wht_rate' => 'WHT (%)',
            'wht_amount' => 'WHT',
            'grand_total' => 'รวมสุทธิ',
            'paid_amount' => 'ยอดชำระแล้ว',
            'balance' => 'ยอดคงเหลือ',
            'status' => 'สถานะ',
            'created_at' => 'วันที่สร้าง',
            'updated_at' => 'วันที่แก้ไข',
        ];
    }

    public function getQuotation()
    {
        return $this->hasOne(Quotation::class, ['id' => 'quotation_id']);
    }

    public function getProject()
    {
        return $this->hasOne(Project::class, ['id' => 'project_id']);
    }

    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    public function getItems()
    {
        return $this->hasMany(InvoiceItem::class, ['invoice_id' => 'id']);
    }

    public function getReceipts()
    {
        return $this->hasMany(Receipt::class, ['invoice_id' => 'id']);
    }

    public function getStatusLabel()
    {
        $statuses = [
            self::STATUS_DRAFT => 'ร่าง',
            self::STATUS_SENT => 'ส่งแล้ว',
            self::STATUS_PARTIAL => 'ชำระบางส่วน',
            self::STATUS_PAID => 'ชำระครบ',
            self::STATUS_OVERDUE => 'เกินกำหนด',
            self::STATUS_CANCELLED => 'ยกเลิก',
        ];
        return $statuses[$this->status] ?? 'ไม่ทราบ';
    }

    public function getStatusBadge()
    {
        $badges = [
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_SENT => 'info',
            self::STATUS_PARTIAL => 'warning',
            self::STATUS_PAID => 'success',
            self::STATUS_OVERDUE => 'danger',
            self::STATUS_CANCELLED => 'dark',
        ];
        $badge = $badges[$this->status] ?? 'secondary';
        return '<span class="badge badge-' . $badge . '">' . $this->getStatusLabel() . '</span>';
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        
        // Calculate balance after save
        $this->balance = $this->grand_total - $this->paid_amount;
        
        // Update status based on balance
        if ($this->balance <= 0) {
            $this->status = self::STATUS_PAID;
        } elseif ($this->paid_amount > 0) {
            $this->status = self::STATUS_PARTIAL;
        } elseif ($this->due_date && $this->due_date < date('Y-m-d') && $this->status != self::STATUS_CANCELLED) {
            $this->status = self::STATUS_OVERDUE;
        }
        
        if ($this->getDirtyAttributes()) {
            $this->updateAttributes(['balance', 'status']);
        }
    }
}
