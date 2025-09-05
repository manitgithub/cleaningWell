<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "projects".
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property int $customer_id
 * @property string|null $start_date
 * @property string|null $end_date
 * @property string $budget
 * @property int $status
 * @property string|null $notes
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Customer $customer
 */
class Project extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'projects';
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
            [['code', 'name', 'customer_id'], 'required'],
            [['customer_id', 'status'], 'integer'],
            [['start_date', 'end_date'], 'date', 'format' => 'php:Y-m-d'],
            [['budget'], 'number'],
            [['notes'], 'string'],
            [['code'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 255],
            [['code'], 'unique'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
            ['status', 'in', 'range' => [0, 1]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'รหัสโครงการ',
            'name' => 'ชื่อโครงการ',
            'customer_id' => 'ลูกค้า',
            'start_date' => 'วันที่เริ่ม',
            'end_date' => 'วันที่สิ้นสุด',
            'budget' => 'งบประมาณ',
            'status' => 'สถานะ',
            'notes' => 'หมายเหตุ',
            'created_at' => 'วันที่สร้าง',
            'updated_at' => 'วันที่แก้ไข',
        ];
    }

    /**
     * Gets query for [[Customer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    /**
     * Get display name
     */
    public function getDisplayName()
    {
        return $this->code . ' - ' . $this->name;
    }

    /**
     * Get status badge
     */
    public function getStatusBadge()
    {
        return $this->status == 1 
            ? '<span class="badge badge-success">Active</span>' 
            : '<span class="badge badge-secondary">Inactive</span>';
    }

    /**
     * Get formatted budget
     */
    public function getFormattedBudget()
    {
        return number_format($this->budget, 2) . ' บาท';
    }

    /**
     * Get status options for dropdown
     */
    public static function getStatusOptions()
    {
        return [
            1 => 'Active',
            0 => 'Inactive',
        ];
    }

    /**
     * Generate project code
     */
    public static function generateCode()
    {
        $prefix = 'PRJ';
        $year = date('Y');
        $lastProject = self::find()
            ->where(['like', 'code', $prefix . $year])
            ->orderBy(['code' => SORT_DESC])
            ->one();
        
        if ($lastProject) {
            $lastNumber = (int) substr($lastProject->code, strlen($prefix . $year));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Before save
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && empty($this->code)) {
                $this->code = self::generateCode();
            }
            if ($insert && !isset($this->status)) {
                $this->status = 1;
            }
            return true;
        }
        return false;
    }
}
