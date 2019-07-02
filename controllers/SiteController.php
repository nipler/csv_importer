<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\Csv;

class SiteController extends Controller
{
	
	public function actions(){
		return [
		   'upload'=>[
			   'class'=>'trntv\filekit\actions\UploadAction',
			   'multiple' => true,
			   'disableCsrf' => true,
			   'responseFormat' => Response::FORMAT_JSON,
			   'responsePathParam' => 'path',
			   'responseBaseUrlParam' => 'base_url',
			   'responseUrlParam' => 'url',
			   'responseDeleteUrlParam' => 'delete_url',
			   'responseMimeTypeParam' => 'type',
			   'responseNameParam' => 'name',
			   'responseSizeParam' => 'size',
			   'deleteRoute' => 'delete',
			   'fileStorage' => 'fileStorage', // Yii::$app->get('fileStorage')
			   'fileStorageParam' => 'fileStorage', // ?fileStorage=someStorageComponent
			   'sessionKey' => '_uploadedFiles',
			   'allowChangeFilestorage' => false,
			   'on afterSave' => function($event) {
                    /* @var $file \League\Flysystem\File */
                    $file = $event->file->getPath();
                    $pathToFile = Yii::getAlias('@webroot') . '/uploads/' . $file;
					if (file_exists($pathToFile) && is_readable($pathToFile)) {
						$data = array();
						if (($handle = fopen($pathToFile, 'r')) !== false) {
							$i = 0;
							while (($row = fgetcsv($handle)) !== false) {
								$model = new Csv();
								$model->key = $row[0];
								$model->value = $row[1];
								
								if ($model->validate()) {
									$model->save();
								} 
							}
							fclose($handle);
						}
					}
               }
		   ]
	   ];
	}
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }


    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

  
}
