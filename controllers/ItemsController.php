<?php

namespace app\controllers;

use Yii;
use app\models\Item;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\User;

/**
 * ItemsController implements the CRUD actions for Item model.
 */
class ItemsController extends Controller
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
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Item models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Item::find()->orderBy(['created_at' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Item model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Item model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Item();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'เพิ่มข้อมูลสินค้า/บริการเรียบร้อยแล้ว');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Item model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'แก้ไขข้อมูลสินค้า/บริการเรียบร้อยแล้ว');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Item model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        try {
            $model->delete();
            Yii::$app->session->setFlash('success', 'ลบข้อมูลสินค้า/บริการเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'ไม่สามารถลบข้อมูลสินค้า/บริการได้ เนื่องจากมีข้อมูลที่เกี่ยวข้อง');
        }

        return $this->redirect(['index']);
    }

    /**
     * Toggle item status
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionToggleStatus($id)
    {
        $model = $this->findModel($id);
        $model->is_active = $model->is_active == 1 ? 0 : 1;
        
        if ($model->save()) {
            $statusText = $model->is_active == 1 ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
            Yii::$app->session->setFlash('success', "เปลี่ยนสถานะสินค้า/บริการเป็น {$statusText} เรียบร้อยแล้ว");
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Bulk update stock quantity
     * @return mixed
     */
    public function actionBulkUpdateStock()
    {
        if (Yii::$app->request->isPost) {
            $stockData = Yii::$app->request->post('stock', []);
            $updated = 0;
            
            foreach ($stockData as $itemId => $quantity) {
                $item = Item::findOne($itemId);
                if ($item) {
                    // Note: stock management removed as not in current schema
                    // $item->stock_quantity = (int) $quantity;
                    // if ($item->save()) {
                    //     $updated++;
                    // }
                }
            }
            
            if ($updated > 0) {
                Yii::$app->session->setFlash('success', "อัปเดตจำนวนคงเหลือ {$updated} รายการเรียบร้อยแล้ว");
            }
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Item model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Item the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Item::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('ไม่พบข้อมูลสินค้า/บริการที่ต้องการ');
    }
}
