<?php

namespace app\controllers;

use Yii;
use app\models\Quotation;
use app\models\QuotationItem;
use app\models\Project;
use app\models\Customer;
use app\models\Item;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Json;

/**
 * QuotationsController implements the CRUD actions for Quotation model.
 */
class QuotationsController extends Controller
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
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'toggle-status' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Quotation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Quotation::find()->with(['project', 'customer']),
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Quotation model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Quotation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Quotation();
        $model->date = date('Y-m-d');
        $model->expire_date = date('Y-m-d', strtotime('+30 days'));
        $model->code = $this->generateCode();
        $model->vat_rate = 7.00;
        $model->wht_rate = 3.00;
        $model->status = 1;

        $projects = Project::find()->where(['status' => 1])->all();
        $customers = Customer::find()->where(['status' => 1])->all();
        $items = Item::find()->where(['is_active' => 1])->all();

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save()) {
                    // Save quotation items
                    $this->saveQuotationItems($model);
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'ใบเสนอราคาถูกสร้างเรียบร้อยแล้ว');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
                $transaction->rollBack();
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
            }
        }

        return $this->render('create', [
            'model' => $model,
            'projects' => $projects,
            'customers' => $customers,
            'items' => $items,
        ]);
    }

    /**
     * Updates an existing Quotation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $projects = Project::find()->where(['status' => 1])->all();
        $customers = Customer::find()->where(['status' => 1])->all();
        $items = Item::find()->where(['is_active' => 1])->all();

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save()) {
                    // Delete existing items and save new ones
                    QuotationItem::deleteAll(['quotation_id' => $model->id]);
                    $this->saveQuotationItems($model);
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'ใบเสนอราคาถูกแก้ไขเรียบร้อยแล้ว');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
                $transaction->rollBack();
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
            }
        }

        return $this->render('update', [
            'model' => $model,
            'projects' => $projects,
            'customers' => $customers,
            'items' => $items,
        ]);
    }

    /**
     * Deletes an existing Quotation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Delete quotation items first
            QuotationItem::deleteAll(['quotation_id' => $id]);
            // Delete quotation
            $model->delete();
            $transaction->commit();
            Yii::$app->session->setFlash('success', 'ใบเสนอราคาถูกลบเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }

        return $this->redirect(['index']);
    }

    /**
     * Toggle status of Quotation model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        $model->status = $model->status == 1 ? 0 : 1;
        
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'สถานะถูกเปลี่ยนเรียบร้อยแล้ว');
        } else {
            Yii::$app->session->setFlash('error', 'เกิดข้อผิดพลาดในการเปลี่ยนสถานะ');
        }

        return $this->redirect(['index']);
    }

    /**
     * Get item details via AJAX
     * @param integer $id
     * @return array
     */
    public function actionGetItem($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $item = Item::findOne($id);
        if ($item) {
            return [
                'success' => true,
                'data' => [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'unit_price' => $item->unit_price,
                    'unit' => $item->unit,
                ]
            ];
        }
        
        return ['success' => false];
    }

    /**
     * Export quotation to PDF
     * @param integer $id
     * @return mixed
     */
    public function actionExportPdf($id)
    {
        $model = $this->findModel($id);
        
        // TODO: Implement PDF export functionality
        Yii::$app->session->setFlash('info', 'ฟีเจอร์ Export PDF จะพัฒนาในขั้นตอนต่อไป');
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Finds the Quotation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Quotation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Quotation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Generate unique quotation code
     * @return string
     */
    private function generateCode()
    {
        $prefix = 'QT';
        $year = date('Y');
        $month = date('m');
        
        $lastQuotation = Quotation::find()
            ->where(['like', 'code', $prefix . $year . $month])
            ->orderBy(['code' => SORT_DESC])
            ->one();
            
        if ($lastQuotation) {
            $lastNumber = (int)substr($lastQuotation->code, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Save quotation items from POST data
     * @param Quotation $model
     */
    private function saveQuotationItems($model)
    {
        $items = Yii::$app->request->post('QuotationItem', []);
        $sortOrder = 1;
        
        foreach ($items as $itemData) {
            if (!empty($itemData['description']) && !empty($itemData['qty']) && !empty($itemData['unit_price'])) {
                $quotationItem = new QuotationItem();
                $quotationItem->quotation_id = $model->id;
                $quotationItem->item_id = !empty($itemData['item_id']) ? $itemData['item_id'] : null;
                $quotationItem->description = $itemData['description'];
                $quotationItem->qty = $itemData['qty'];
                $quotationItem->unit_price = $itemData['unit_price'];
                $quotationItem->line_discount = !empty($itemData['line_discount']) ? $itemData['line_discount'] : 0;
                $quotationItem->line_total = $itemData['line_total'];
                $quotationItem->sort_order = $sortOrder++;
                $quotationItem->save();
            }
        }
    }
}
