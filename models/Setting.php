<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "settings".
 *
 * @property int $id
 * @property string $key
 * @property string|null $value
 */
class Setting extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key'], 'required'],
            [['value'], 'string'],
            [['key'], 'string', 'max' => 255],
            [['key'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'value' => 'Value',
        ];
    }

    /**
     * Get setting value by key
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $setting = static::findOne(['key' => $key]);
        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting value by key
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public static function set($key, $value)
    {
        $setting = static::findOne(['key' => $key]);
        if (!$setting) {
            $setting = new static();
            $setting->key = $key;
        }
        $setting->value = $value;
        return $setting->save();
    }

    /**
     * Get multiple settings
     * @param array $keys
     * @return array
     */
    public static function getMultiple($keys)
    {
        $settings = static::find()->where(['key' => $keys])->all();
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting->key] = $setting->value;
        }
        
        // Fill missing keys with null
        foreach ($keys as $key) {
            if (!isset($result[$key])) {
                $result[$key] = null;
            }
        }
        
        return $result;
    }

    /**
     * Set multiple settings
     * @param array $settings key => value pairs
     * @return bool
     */
    public static function setMultiple($settings)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($settings as $key => $value) {
                if (!static::set($key, $value)) {
                    throw new \Exception("Failed to save setting: $key");
                }
            }
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
    }

    /**
     * Initialize default settings
     */
    public static function initializeDefaults()
    {
        $defaults = [
            // VAT & WHT
            'vat_rate' => '7',
            'wht_rate' => '3',
            'vat_enabled' => '1',
            'wht_enabled' => '1',
            
            // Document Numbering
            'quotation_prefix' => 'QT',
            'quotation_format' => '{prefix}{year}{month}-{number:4}',
            'quotation_counter' => '1',
            'invoice_prefix' => 'INV',
            'invoice_format' => '{prefix}{year}{month}-{number:4}',
            'invoice_counter' => '1',
            'receipt_prefix' => 'RC',
            'receipt_format' => '{prefix}{year}{month}-{number:4}',
            'receipt_counter' => '1',
            
            // Company Info
            'company_name' => 'CleaningWell Co., Ltd.',
            'company_address' => '',
            'company_tax_id' => '',
            'company_phone' => '',
            'company_email' => '',
            'company_logo' => '',
            
            // GPS & Geofence
            'gps_accuracy_required' => '50',
            'geofence_radius_default' => '100',
            'allow_manual_checkin' => '0',
        ];

        foreach ($defaults as $key => $value) {
            if (!static::findOne(['key' => $key])) {
                static::set($key, $value);
            }
        }
    }
}
