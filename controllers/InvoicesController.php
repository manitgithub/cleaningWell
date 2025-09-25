<?php

namespace app\controllers;

use Yii;
use app\models\Invoice;
use app\models\InvoiceItem;
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

/**
 * InvoicesController implements the CRUD actions for Invoice model.
 */
class InvoicesController extends Controller
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
                    'update-status' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Invoice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Invoice::find()->with(['project', 'customer']),
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Invoice model.
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
     * Creates a new Invoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Invoice();
        $model->date = date('Y-m-d');
        $model->due_date = date('Y-m-d', strtotime('+30 days'));
        $model->code = $this->generateCode();
        $model->vat_rate = 7.00;
        $model->wht_rate = 3.00;
        $model->status = Invoice::STATUS_DRAFT;
        $model->paid_amount = 0;

        $projects = Project::find()->where(['status' => 1])->all();
        $customers = Customer::find()->where(['status' => 1])->all();
        $items = Item::find()->where(['is_active' => 1])->all();

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save()) {
                    // Save invoice items
                    $this->saveInvoiceItems($model);
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'ใบแจ้งหนี้ถูกสร้างเรียบร้อยแล้ว');
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
     * Show list of quotations to create invoice from
     * @return mixed
     */
    public function actionCreateFromQuotationList()
    {
        return $this->render('create-from-quotation-list');
    }

    /**
     * Creates a new Invoice from Quotation.
     * @param integer $id Quotation ID
     * @return mixed
     */
    public function actionCreateFromQuotation($id)
    {
        $quotation = Quotation::findOne($id);
        if (!$quotation) {
            throw new NotFoundHttpException('ใบเสนอราคาที่ต้องการไม่พบ');
        }

        $model = new Invoice();
        $model->quotation_id = $quotation->id;
        $model->project_id = $quotation->project_id;
        $model->customer_id = $quotation->customer_id;
        $model->date = date('Y-m-d');
        $model->due_date = date('Y-m-d', strtotime('+30 days'));
        $model->code = $this->generateInvoiceCode();
        $model->subject = $quotation->subject;
        $model->notes = $quotation->notes;
        $model->sub_total = $quotation->sub_total;
        $model->discount_total = $quotation->discount_total;
        $model->vat_rate = $quotation->vat_rate;
        $model->vat_amount = $quotation->vat_amount;
        $model->wht_rate = $quotation->wht_rate;
        $model->wht_amount = $quotation->wht_amount;
        $model->grand_total = $quotation->grand_total;
        $model->status = Invoice::STATUS_DRAFT;
        $model->paid_amount = 0;
        $model->balance = $quotation->grand_total;

        $projects = Project::find()->where(['status' => 1])->all();
        $customers = Customer::find()->where(['status' => 1])->all();
        $items = Item::find()->where(['is_active' => 1])->all();

        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save()) {
                    // Copy quotation items to invoice items
                    foreach ($quotation->items as $quotationItem) {
                        $invoiceItem = new InvoiceItem();
                        $invoiceItem->invoice_id = $model->id;
                        $invoiceItem->item_id = $quotationItem->item_id;
                        $invoiceItem->description = $quotationItem->description;
                        $invoiceItem->qty = $quotationItem->qty;
                        $invoiceItem->unit_price = $quotationItem->unit_price;
                        $invoiceItem->line_discount = $quotationItem->line_discount;
                        $invoiceItem->line_total = $quotationItem->line_total;
                        $invoiceItem->sort_order = $quotationItem->sort_order;
                        $invoiceItem->save();
                    }
                    
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'ใบแจ้งหนี้ถูกสร้างจากใบเสนอราคาเรียบร้อยแล้ว');
                    return $this->redirect(['view', 'id' => $model->id]);
                }
                $transaction->rollBack();
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
            }
        }

        return $this->render('create-from-quotation', [
            'model' => $model,
            'quotation' => $quotation,
            'projects' => $projects,
            'customers' => $customers,
            'items' => $items,
        ]);
    }

    /**
     * Updates an existing Invoice model.
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
                    InvoiceItem::deleteAll(['invoice_id' => $model->id]);
                    $this->saveInvoiceItems($model);
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', 'ใบแจ้งหนี้ถูกแก้ไขเรียบร้อยแล้ว');
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
     * Deletes an existing Invoice model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Delete invoice items first
            InvoiceItem::deleteAll(['invoice_id' => $id]);
            // Delete invoice
            $model->delete();
            $transaction->commit();
            Yii::$app->session->setFlash('success', 'ใบแจ้งหนี้ถูกลบเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }

        return $this->redirect(['index']);
    }

    /**
     * Update invoice status.
     * @param integer $id
     * @param integer $status
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateStatus($id, $status)
    {
        $model = $this->findModel($id);
        $model->status = $status;
        
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'สถานะถูกเปลี่ยนเรียบร้อยแล้ว');
        } else {
            Yii::$app->session->setFlash('error', 'เกิดข้อผิดพลาดในการเปลี่ยนสถานะ');
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Export invoice to PDF
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
     * Finds the Invoice model based on its primary key value.
     * @param integer $id
     * @return Invoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Invoice::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Generate unique invoice code
     * @return string
     */
    private function generateCode()
    {
        $prefix = 'INV';
        $year = date('Y');
        $month = date('m');
        
        $lastInvoice = Invoice::find()
            ->where(['like', 'code', $prefix . $year . $month])
            ->orderBy(['code' => SORT_DESC])
            ->one();
            
        if ($lastInvoice) {
            $lastNumber = (int)substr($lastInvoice->code, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Save invoice items from POST data
     * @param Invoice $model
     */
    private function saveInvoiceItems($model)
    {
        $items = Yii::$app->request->post('InvoiceItem', []);
        $sortOrder = 1;
        
        foreach ($items as $itemData) {
            if (!empty($itemData['description']) && !empty($itemData['qty']) && !empty($itemData['unit_price'])) {
                $invoiceItem = new InvoiceItem();
                $invoiceItem->invoice_id = $model->id;
                $invoiceItem->item_id = !empty($itemData['item_id']) ? $itemData['item_id'] : null;
                $invoiceItem->description = $itemData['description'];
                $invoiceItem->qty = $itemData['qty'];
                $invoiceItem->unit_price = $itemData['unit_price'];
                $invoiceItem->line_discount = !empty($itemData['line_discount']) ? $itemData['line_discount'] : 0;
                $invoiceItem->line_total = $itemData['line_total'];
                $invoiceItem->sort_order = $sortOrder++;
                $invoiceItem->save();
            }
        }
    }
}
