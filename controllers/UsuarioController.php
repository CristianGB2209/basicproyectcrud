<?php

namespace app\controllers;

use app\models\Usuario;
use app\models\UsuarioSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use kartik\mpdf\Pdf;


use yii\web\UploadedFile;


/**
 * UsuarioController implements the CRUD actions for Usuario model.
 */
class UsuarioController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                
                'access'=>[
                    'class'=> AccessControl::className(),
                    'rules'=>[
                
                        [
                            
                        'allow'=>true,
                        'roles'=>['@']
                        ]
                
                        ]
                
                        ],
                
                
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Usuario models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UsuarioSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Usuario model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Usuario model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Usuario();

        $this->subirFoto($model);

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Usuario model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $this->subirFoto($model);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Usuario model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
       $model=$this->findModel($id);
       
       if(file_exists($model->imagen)){
        unlink($model->imagen);

       }
       
       $model->delete();

        return $this->redirect(['index']);
    }

public function actionViewPdf($id)
{
    $usuario = Usuario::findOne($id);

    if ($usuario !== null) {
        $pdfContent = $this->renderPartial('pdf_template', [
            'usuario' => $usuario,
        ]);

        $pdf = new Pdf([     
            
            'content' => $pdfContent,

            'methods' => [
                'SetTitle' => 'Reporte de Usuario',
                'SetHeader' => ['Fecha: ' . date('r')],
                'SetFooter' => ['|Página {PAGENO}|'],
            ]
        ]);

        return $pdf->render();
    } else {
        throw new NotFoundHttpException('El usuario no existe.');
    }
    
}


    /**
     * Finds the Usuario model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Usuario the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Usuario::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function subirFoto(Usuario $model) {

        if ($this->request->isPost) {

            if ($model->load($this->request->post()) ) {
                



                $model->archivo= UploadedFile::getInstance($model,'archivo');

                if($model->validate()) {

                    if( $model->archivo ) {

                        if(file_exists($model->imagen)){
                            unlink($model->imagen);
                    
                           }
                           
                        $rutaArchivo='uploads/'.time()."_".$model->archivo->baseName.".".$model->archivo->extension;

                        if( $model->archivo->saveAs($rutaArchivo)){

                            $model->imagen=$rutaArchivo;
                        }

                    }
                }
                
                if( $model->save(false)){

                    return $this->redirect(['index']);

                }

            }
        } else {
            $model->loadDefaultValues();
        }
    }
}
