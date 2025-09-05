<?php

namespace app\controllers;

use Yii;
use app\models\Setting;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use app\models\User;

/**
 * SettingsController implements the CRUD actions for Setting model.
 */
class SettingsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->role == User::ROLE_ADMIN;
                        }
                    ],
                ],
            ],
        ];
    }

    /**
     * System settings index page
     * @return mixed
     */
    public function actionIndex()
    {
        $settingKeys = [
            // VAT & WHT
            'vat_rate', 'wht_rate', 'vat_enabled', 'wht_enabled',
            
            // Document Numbering
            'quotation_prefix', 'quotation_format', 'quotation_counter',
            'invoice_prefix', 'invoice_format', 'invoice_counter',
            'receipt_prefix', 'receipt_format', 'receipt_counter',
            
            // Company Info
            'company_name', 'company_address', 'company_tax_id',
            'company_phone', 'company_email', 'company_logo',
            
            // GPS & Geofence
            'gps_accuracy_required', 'geofence_radius_default', 'allow_manual_checkin'
        ];

        $settings = Setting::getMultiple($settingKeys);

        if (Yii::$app->request->isPost) {
            $postData = Yii::$app->request->post();
            
            // Handle file upload for logo
            $logoFile = UploadedFile::getInstanceByName('company_logo');
            if ($logoFile) {
                $logoPath = 'uploads/company/' . uniqid() . '.' . $logoFile->extension;
                if ($logoFile->saveAs($logoPath)) {
                    $postData['company_logo'] = $logoPath;
                }
            } else {
                // Keep existing logo if no new file uploaded
                $postData['company_logo'] = $settings['company_logo'];
            }

            if (Setting::setMultiple($postData)) {
                Yii::$app->session->setFlash('success', 'บันทึกการตั้งค่าเรียบร้อยแล้ว');
                return $this->refresh();
            } else {
                Yii::$app->session->setFlash('error', 'เกิดข้อผิดพลาดในการบันทึกการตั้งค่า');
            }
        }

        return $this->render('index', [
            'settings' => $settings,
        ]);
    }

    /**
     * Initialize default settings
     * @return mixed
     */
    public function actionInitDefaults()
    {
        Setting::initializeDefaults();
        Yii::$app->session->setFlash('success', 'เริ่มต้นค่าเริ่มต้นเรียบร้อยแล้ว');
        return $this->redirect(['index']);
    }

    /**
     * Reset document counters
     * @return mixed
     */
    public function actionResetCounters()
    {
        $counters = [
            'quotation_counter' => '1',
            'invoice_counter' => '1',
            'receipt_counter' => '1',
        ];

        if (Setting::setMultiple($counters)) {
            Yii::$app->session->setFlash('success', 'รีเซ็ตตัวนับเอกสารเรียบร้อยแล้ว');
        } else {
            Yii::$app->session->setFlash('error', 'เกิดข้อผิดพลาดในการรีเซ็ตตัวนับเอกสาร');
        }

        return $this->redirect(['index']);
    }
}
