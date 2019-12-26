<?php

namespace app\modules\admin\controllers;

use app\models\Statistic;
use Yii;
use app\models\Report;
use app\models\ReportSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ReportController implements the CRUD actions for Report model.
 */
class ReportController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Report models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ReportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Report model.
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
     * Creates a new Report model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Report();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Report model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Report model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Report model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Report the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Report::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionCloseOrder($id) {
        $report = Report::find()
            ->where(['id' => $id])
            ->with('order')
            ->one();

        $order = $report->order;

        $order->status_id = 2;
        $order->save();
        $report->delete();
        return $this->redirect(['index']);

    }

    public function actionCanselOrder($id) {
        $report = Report::find()
            ->where(['id' => $id])
            ->with('order')
            ->one();

        $order = $report->order;
        $status_id = null;

        $report->user_id == $order->customer_id ? $status_id = 4: $status_id = 5;

        $order->status_id = $status_id;
        $order->save();

        $customer_statistic = Statistic::find()
            ->where(['user_id' => $order->customer_id])
            ->one();

        $executor_statistic = Statistic::find()
            ->where(['user_id' => $order->executor_id])
            ->one();

        $customer_statistic->canseled_orders = $customer_statistic->canseled_orders + 1;
        $customer_statistic->save();

        $executor_statistic->canseled_orders = $executor_statistic->canseled_orders + 1;
        $executor_statistic->save();

        $report->delete();

        return $this->redirect(['index']);
    }
}
