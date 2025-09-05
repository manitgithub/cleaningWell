<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "quotation_items".
 *
 * @property int $id
 * @property int $quotation_id
 * @property int $item_id
 * @property string $description
 * @property string $qty
 * @property string $unit_price
 * @property string $line_discount
 * @property string $line_total
 * @property int $sort_order
 *
 * @property Quotation $quotation
 * @property Item $item
 */
class QuotationItem extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quotation_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['quotation_id', 'item_id', 'description', 'qty', 'unit_price'], 'required'],
            [['quotation_id', 'item_id', 'sort_order'], 'integer'],
            [['qty', 'unit_price', 'line_discount', 'line_total'], 'number', 'min' => 0],
            [['description'], 'string', 'max' => 255],
            [['line_discount'], 'default', 'value' => 0],
            [['sort_order'], 'default', 'value' => 0],
            [['quotation_id'], 'exist', 'targetClass' => Quotation::class, 'targetAttribute' => 'id'],
            [['item_id'], 'exist', 'targetClass' => Item::class, 'targetAttribute' => 'id'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'quotation_id' => 'ใบเสนอราคา',
            'item_id' => 'รายการ',
            'description' => 'รายละเอียด',
            'qty' => 'จำนวน',
            'unit_price' => 'ราคาต่อหน่วย',
            'line_discount' => 'ส่วนลด',
            'line_total' => 'รวม',
            'sort_order' => 'ลำดับ',
        ];
    }

    /**
     * Get quotation relation
     */
    public function getQuotation()
    {
        return $this->hasOne(Quotation::class, ['id' => 'quotation_id']);
    }

    /**
     * Get item relation
     */
    public function getItem()
    {
        return $this->hasOne(Item::class, ['id' => 'item_id']);
    }

    /**
     * Calculate line total before save
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->line_total = ($this->qty * $this->unit_price) - $this->line_discount;
            return true;
        }
        return false;
    }

    /**
     * Get formatted unit price
     */
    public function getFormattedUnitPrice()
    {
        return Yii::$app->formatter->asCurrency($this->unit_price);
    }

    /**
     * Get formatted line total
     */
    public function getFormattedLineTotal()
    {
        return Yii::$app->formatter->asCurrency($this->line_total);
    }

    /**
     * Get item name with unit
     */
    public function getItemNameWithUnit()
    {
        return $this->item ? $this->item->name . ' (' . $this->item->unit . ')' : '';
    }
}
