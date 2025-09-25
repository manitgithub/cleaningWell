<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "invoice_items".
 *
 * @property int $id
 * @property int $invoice_id
 * @property int $item_id
 * @property string $description
 * @property float $qty
 * @property float $unit_price
 * @property float $line_discount
 * @property float $line_total
 * @property int $sort_order
 *
 * @property Invoice $invoice
 * @property Item $item
 */
class InvoiceItem extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'invoice_items';
    }

    public function rules()
    {
        return [
            [['invoice_id', 'description', 'qty', 'unit_price', 'line_total'], 'required'],
            [['invoice_id', 'item_id', 'sort_order'], 'integer'],
            [['description'], 'string'],
            [['qty', 'unit_price', 'line_discount', 'line_total'], 'number'],
            [['invoice_id'], 'exist', 'skipOnError' => true, 'targetClass' => Invoice::class, 'targetAttribute' => ['invoice_id' => 'id']],
            [['item_id'], 'exist', 'skipOnError' => true, 'targetClass' => Item::class, 'targetAttribute' => ['item_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoice_id' => 'ใบแจ้งหนี้',
            'item_id' => 'สินค้า/บริการ',
            'description' => 'รายละเอียด',
            'qty' => 'จำนวน',
            'unit_price' => 'ราคาต่อหน่วย',
            'line_discount' => 'ส่วนลด',
            'line_total' => 'รวมรายการ',
            'sort_order' => 'ลำดับ',
        ];
    }

    public function getInvoice()
    {
        return $this->hasOne(Invoice::class, ['id' => 'invoice_id']);
    }

    public function getItem()
    {
        return $this->hasOne(Item::class, ['id' => 'item_id']);
    }
}
