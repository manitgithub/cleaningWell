<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "quotations".
 *
 * @property int $id
 * @property string $code
 * @property int $project_id
 * @property int $customer_id
 * @property string $date
 * @property string $valid_until
 * @property string $sub_total
 * @property string $discount_total
 * @property string $vat_rate
 * @property string $vat_amount
 * @property string $wht_rate
 * @property string $wht_amount
 * @property string $grand_total
 * @property string $payment_terms
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Customer $customer
 * @property Project $project
 * @property QuotationItem[] $quotationItems
 * @property Invoice[] $invoices
 */
class Quotation extends ActiveRecord
{
    const STATUS_DRAFT = 1;
    const STATUS_SENT = 2;
    const STATUS_APPROVED = 3;
    const STATUS_REJECTED = 4;
    const STATUS_CONVERTED = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quotations';
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_id', 'customer_id', 'date', 'valid_until', 'payment_terms'], 'required'],
            [['project_id', 'customer_id', 'status'], 'integer'],
            [['date', 'valid_until', 'created_at', 'updated_at'], 'safe'],
            [['sub_total', 'discount_total', 'vat_rate', 'vat_amount', 'wht_rate', 'wht_amount', 'grand_total'], 'number', 'min' => 0],
            [['code'], 'string', 'max' => 32],
            [['payment_terms'], 'string', 'max' => 255],
            [['code'], 'unique'],
            [['customer_id'], 'exist', 'targetClass' => Customer::class, 'targetAttribute' => 'id'],
            [['project_id'], 'exist', 'targetClass' => Project::class, 'targetAttribute' => 'id'],
            ['status', 'default', 'value' => self::STATUS_DRAFT],
            ['status', 'in', 'range' => [self::STATUS_DRAFT, self::STATUS_SENT, self::STATUS_APPROVED, self::STATUS_REJECTED, self::STATUS_CONVERTED]],
            ['vat_rate', 'default', 'value' => 7],
            ['wht_rate', 'default', 'value' => 3],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'เลขที่ใบเสนอราคา',
            'project_id' => 'โครงการ',
            'customer_id' => 'ลูกค้า',
            'date' => 'วันที่',
            'valid_until' => 'ใช้ได้ถึงวันที่',
            'sub_total' => 'ยอดรวม',
            'discount_total' => 'ส่วนลด',
            'vat_rate' => 'อัตรา VAT (%)',
            'vat_amount' => 'จำนวนเงิน VAT',
            'wht_rate' => 'อัตราหักภาษี ณ ที่จ่าย (%)',
            'wht_amount' => 'จำนวนเงินหักภาษี',
            'grand_total' => 'ยอดสุทธิ',
            'payment_terms' => 'เงื่อนไขการชำระเงิน',
            'status' => 'สถานะ',
            'created_at' => 'วันที่สร้าง',
            'updated_at' => 'วันที่อัปเดต',
        ];
    }

    /**
     * Generate next quotation code
     */
    public function generateCode()
    {
        $year = date('Y');
        $prefix = 'QT' . $year;
        
        $lastCode = self::find()
            ->where(['like', 'code', $prefix])
            ->orderBy('id DESC')
            ->one();
            
        if ($lastCode) {
            $lastNumber = (int)substr($lastCode->code, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && empty($this->code)) {
                $this->code = $this->generateCode();
            }
            return true;
        }
        return false;
    }

    /**
     * Get customer relation
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    /**
     * Get project relation
     */
    public function getProject()
    {
        return $this->hasOne(Project::class, ['id' => 'project_id']);
    }

    /**
     * Get quotation items relation
     */
    public function getQuotationItems()
    {
        return $this->hasMany(QuotationItem::class, ['quotation_id' => 'id'])->orderBy('sort_order');
    }

    /**
     * Get invoices relation
     */
    public function getInvoices()
    {
        return $this->hasMany(Invoice::class, ['quotation_id' => 'id']);
    }

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        $statuses = self::getStatusOptions();
        return $statuses[$this->status] ?? 'ไม่ทราบสถานะ';
    }

    /**
     * Get status badge
     */
    public function getStatusBadge()
    {
        $badges = [
            self::STATUS_DRAFT => '<span class="badge badge-secondary">แบบร่าง</span>',
            self::STATUS_SENT => '<span class="badge badge-info">ส่งแล้ว</span>',
            self::STATUS_APPROVED => '<span class="badge badge-success">อนุมัติ</span>',
            self::STATUS_REJECTED => '<span class="badge badge-danger">ปฏิเสธ</span>',
            self::STATUS_CONVERTED => '<span class="badge badge-warning">แปลงเป็น Invoice</span>',
        ];
        return $badges[$this->status] ?? '<span class="badge badge-light">ไม่ทราบ</span>';
    }

    /**
     * Get status options for dropdown
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_DRAFT => 'แบบร่าง',
            self::STATUS_SENT => 'ส่งแล้ว',
            self::STATUS_APPROVED => 'อนุมัติ',
            self::STATUS_REJECTED => 'ปฏิเสธ',
            self::STATUS_CONVERTED => 'แปลงเป็น Invoice',
        ];
    }

    /**
     * Calculate totals
     */
    public function calculateTotals()
    {
        $this->sub_total = 0;
        foreach ($this->quotationItems as $item) {
            $this->sub_total += $item->line_total;
        }
        
        // Calculate after discount
        $afterDiscount = $this->sub_total - $this->discount_total;
        
        // Calculate VAT
        $this->vat_amount = ($afterDiscount * $this->vat_rate) / 100;
        
        // Calculate WHT
        $this->wht_amount = ($afterDiscount * $this->wht_rate) / 100;
        
        // Calculate grand total
        $this->grand_total = $afterDiscount + $this->vat_amount - $this->wht_amount;
    }

    /**
     * Check if can be converted to invoice
     */
    public function canConvertToInvoice()
    {
        return $this->status == self::STATUS_APPROVED;
    }

    /**
     * Get formatted date
     */
    public function getFormattedDate()
    {
        return Yii::$app->formatter->asDate($this->date);
    }

    /**
     * Get formatted valid until date
     */
    public function getFormattedValidUntil()
    {
        return Yii::$app->formatter->asDate($this->valid_until);
    }

    /**
     * Get formatted grand total
     */
    public function getFormattedGrandTotal()
    {
        return Yii::$app->formatter->asCurrency($this->grand_total);
    }
}
