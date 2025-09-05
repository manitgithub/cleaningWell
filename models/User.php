<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $display_name
 * @property string $phone
 * @property string $email
 * @property integer $role 1=admin, 2=housekeeper
 * @property integer $status
 * @property string $device_id
 * @property string $last_login_at
 * @property string $created_at
 * @property string $updated_at
 */
class User extends ActiveRecord implements IdentityInterface
{
    const ROLE_ADMIN = 1;
    const ROLE_HOUSEKEEPER = 2;
    
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public $password; // For form input only

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%users}}';
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
            [['username'], 'required'],
            [['username'], 'string', 'max' => 64],
            [['username'], 'unique'],
            [['password'], 'required', 'on' => 'create'],
            [['password'], 'string', 'min' => 6],
            [['display_name'], 'string', 'max' => 128],
            [['phone'], 'string', 'max' => 32],
            [['email'], 'email'],
            [['role'], 'in', 'range' => [self::ROLE_ADMIN, self::ROLE_HOUSEKEEPER]],
            [['status'], 'in', 'range' => [self::STATUS_INACTIVE, self::STATUS_ACTIVE]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'ชื่อผู้ใช้',
            'password' => 'รหัสผ่าน',
            'display_name' => 'ชื่อแสดง',
            'phone' => 'เบอร์โทร',
            'email' => 'อีเมล',
            'role' => 'บทบาท',
            'status' => 'สถานะ',
            'created_at' => 'วันที่สร้าง',
            'updated_at' => 'วันที่แก้ไข',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // Not implemented for basic auth
        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return null; // Not used in basic session auth
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return true; // Not used in basic session auth
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Before save event
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->password) {
                $this->setPassword($this->password);
            }
            return true;
        }
        return false;
    }

    /**
     * Get role name
     */
    public function getRoleName()
    {
        $roles = [
            self::ROLE_ADMIN => 'ผู้ดูแลระบบ',
            self::ROLE_HOUSEKEEPER => 'แม่บ้าน',
        ];
        return $roles[$this->role] ?? 'ไม่ระบุ';
    }

    /**
     * Get status name
     */
    public function getStatusName()
    {
        $statuses = [
            self::STATUS_INACTIVE => 'ไม่ใช้งาน',
            self::STATUS_ACTIVE => 'ใช้งาน',
        ];
        return $statuses[$this->status] ?? 'ไม่ระบุ';
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is housekeeper
     */
    public function isHousekeeper()
    {
        return $this->role === self::ROLE_HOUSEKEEPER;
    }

    /**
     * Update last login time
     */
    public function updateLastLogin()
    {
        $this->last_login_at = new Expression('NOW()');
        $this->save(false);
    }
}
