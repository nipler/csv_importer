<?php

namespace app\controllers;

use yii\rest\ActiveController;
use yii\web\Response;

use Yii;
use app\models\Csv;
use app\models\search\CsvSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\httpclient\XmlParser;
/**
 * CsvController implements the CRUD actions for Csv model.
 */
class ApiController extends ActiveController
{
	
	public $modelClass = 'app\models\Csv';
	
	protected function verbs()
	{
		return [
			'csv' => ['GET', 'HEAD'],
			'change' => ['POST'],
		];
	}
	
	public function behaviors()
	{
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
			'class' => 'yii\filters\ContentNegotiator',
			'formats' => [
				'application/json' => Response::FORMAT_XML,
			]
        ];
		
        return $behaviors;
	}	
	
	public function actions() {
		$actions = parent::actions();

		// disable the "delete" and "create" actions
		unset($actions['delete'], $actions['view'], $actions['create'], $actions['update'], $actions['index']);

		return $actions;
	}
	
	/**
     * @inheritdoc
     */
    /* public function actions()
    {
        return [
            'csv' => 'mongosoft\soapserver\Action',
        ];
    } */
	
	/**
     * @param string $name
     * @return string
     * @soap
     */
    public function actionCsv()
    {
		$searchModel = new CsvSearch();
        return $searchModel->search(Yii::$app->request->queryParams);
    }
	
	/**
     * @param string $name
     * @return string
     * @soap
     */
    public function actionChange($id)
    {
		$file = Yii::$app->request->getRawBody();
		$model = $this->findModel($id);
		
		$xmlData = simplexml_load_string($file); 
		if($xmlData) {
			switch($xmlData->action) {
				case 'add':
					$model->value = $model->value + $xmlData->value;
				break;
				
				case 'sub':
					$model->value = $model->value - $xmlData->value;
				break;
				
				case 'mul':
					$model->value = round($model->value * $xmlData->value);
				break;
				
				case 'div':
					$model->value = round($model->value / $xmlData->value);
				break;
			}
			
			if($model->save()) {
				return $model;
			}
			else {
				return $model->errors;
			}
		}
		
		return [];
    }
	

    


    /**
     * Finds the Csv model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Csv the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Csv::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
