<?php

namespace app\controllers;

use app\models\Paciente;
use app\models\PacienteSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;
use app\models\Cita;
use app\models\FotoSesion;
use app\models\Foto;
use Yii;

/**
 * PacienteController implements the CRUD actions for Paciente model.
 */
class PacienteController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                        'cita-foto-sesion-ajax' => ['GET'],
                        'cita-fotos-ajax' => ['GET'],
                        'cita-foto-upload-ajax' => ['POST'],
                        'cita-foto-delete-ajax' => ['POST'],

                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Paciente models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PacienteSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Paciente model.
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
     * Creates a new Paciente model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Paciente();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {

                $model->usuario_id = Yii::$app->user->id;
                $timestampActual = time();
                $model->created_at = $timestampActual;
                $model->updated_at = $timestampActual;

                if (empty($model->status)) {
                    $model->status = 1;
                }

                // INTENTA GUARDAR
                if ($model->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
    /**
     * Updates an existing Paciente model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Paciente model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Paciente model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Paciente the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Paciente::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionCitaFotoSesionAjax($cita_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $cita = Cita::findOne((int)$cita_id);
        if (!$cita) return ['success' => false, 'message' => 'Cita no encontrada.'];

        if (empty($cita->paciente_id)) {
            return ['success' => false, 'message' => 'La cita no tiene paciente asignado.'];
        }

        $sesion = FotoSesion::find()->where(['cita_id' => (int)$cita_id])->one();

        if (!$sesion) {
            $sesion = new FotoSesion();
            $sesion->paciente_id = (int)$cita->paciente_id;
            $sesion->cita_id = (int)$cita->id;
            $sesion->fecha_sesion = date('Y-m-d');
            $sesion->titulo = 'Cita #' . $cita->id;
            $sesion->notas = null;
            $sesion->created_by = !Yii::$app->user->isGuest ? (int)Yii::$app->user->id : null;
            $sesion->created_at = time();
            $sesion->updated_at = time();

            if (!$sesion->save()) {
                return ['success' => false, 'message' => 'No se pudo crear la sesión.', 'errors' => $sesion->getErrors()];
            }
        }

        return ['success' => true, 'sesion' => ['id' => (int)$sesion->id]];
    }

    public function actionCitaFotosAjax($cita_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $sesion = FotoSesion::find()->where(['cita_id' => (int)$cita_id])->one();
        if (!$sesion) return ['success' => true, 'fotos' => []];

        $rows = Foto::find()
            ->where(['foto_sesion_id' => (int)$sesion->id])
            ->orderBy(['orden' => SORT_ASC, 'id' => SORT_DESC])
            ->asArray()
            ->all();

        $base = rtrim(Yii::$app->request->baseUrl, '/');

        $fotos = array_map(function ($r) use ($base) {
            $path = ltrim($r['archivo'], '/');
            return [
                'id' => (int)$r['id'],
                'url' => $base . '/' . $path,
                'archivo' => $r['archivo'],
                'mime' => $r['mime'] ?? null,
                'size_bytes' => (int)($r['size_bytes'] ?? 0),
                'etiqueta' => $r['etiqueta'] ?? null,
                'orden' => (int)($r['orden'] ?? 1),
            ];
        }, $rows);

        return ['success' => true, 'fotos' => $fotos];
    }

    public function actionCitaFotoUploadAjax()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $citaId = (int)Yii::$app->request->post('cita_id');
        if (!$citaId) return ['success' => false, 'message' => 'Falta cita_id.'];

        $cita = \app\models\Cita::findOne($citaId);
        if (!$cita) return ['success' => false, 'message' => 'Cita no encontrada.'];

        if (empty($cita->paciente_id)) {
            return ['success' => false, 'message' => 'La cita no tiene paciente asignado.'];
        }

        $sesion = \app\models\FotoSesion::find()->where(['cita_id' => $citaId])->one();
        if (!$sesion) {
            $sesion = new \app\models\FotoSesion();
            $sesion->paciente_id = (int)$cita->paciente_id;
            $sesion->cita_id = (int)$cita->id;
            $sesion->fecha_sesion = date('Y-m-d');
            $sesion->titulo = 'Cita #' . $cita->id;
            $sesion->created_by = !Yii::$app->user->isGuest ? (int)Yii::$app->user->id : null;
            $sesion->created_at = time();
            $sesion->updated_at = time();

            if (!$sesion->save()) {
                return ['success' => false, 'message' => 'No se pudo crear la sesión.', 'errors' => $sesion->getErrors()];
            }
        }

        $files = UploadedFile::getInstancesByName('fotos');
        if (!$files || count($files) === 0) {
            return ['success' => false, 'message' => 'No se recibieron archivos (fotos).'];
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 10 * 1024 * 1024;

        $pacienteId = (int)$cita->paciente_id;

        $dirAbs = Yii::getAlias('@webroot/uploads/pacientes/' . $pacienteId . '/citas/' . $citaId);
        if (!is_dir($dirAbs)) @mkdir($dirAbs, 0775, true);

        $maxOrden = (int)\app\models\Foto::find()->where(['foto_sesion_id' => (int)$sesion->id])->max('orden');
        $orden = $maxOrden ? ($maxOrden + 1) : 1;

        $guardadas = [];
        $errores = [];

        foreach ($files as $i => $file) {
            try {
                if (!in_array($file->type, $allowed, true)) {
                    $errores[] = $file->name . ': formato no permitido.';
                    continue;
                }
                if ($file->size > $maxSize) {
                    $errores[] = $file->name . ': excede 10 MB.';
                    continue;
                }

                $ext = strtolower($file->getExtension());
                $safeName = 'cita_' . $citaId . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

                $abs = $dirAbs . DIRECTORY_SEPARATOR . $safeName;
                if (!$file->saveAs($abs)) {
                    $errores[] = $file->name . ': no se pudo guardar archivo.';
                    continue;
                }

                $rel = 'uploads/pacientes/' . $pacienteId . '/citas/' . $citaId . '/' . $safeName;

                $imgW = null;
                $imgH = null;
                $info = @getimagesize($abs);
                if (is_array($info)) {
                    $imgW = $info[0] ?? null;
                    $imgH = $info[1] ?? null;
                }

                $foto = new \app\models\Foto();
                $foto->foto_sesion_id = (int)$sesion->id;
                $foto->archivo = $rel;
                $foto->mime = $file->type;
                $foto->size_bytes = (int)$file->size;
                $foto->ancho = $imgW;
                $foto->alto = $imgH;
                $foto->etiqueta = null;
                $foto->orden = $orden++;
                $foto->created_at = time();

                if (!$foto->save()) {
                    @unlink($abs);
                    $errores[] = $file->name . ': no se pudo guardar en BD.';
                    continue;
                }

                $guardadas[] = [
                    'id' => (int)$foto->id,
                    'url' => rtrim(Yii::$app->request->baseUrl, '/') . '/' . ltrim($rel, '/'),
                    'archivo' => $rel,
                    'orden' => (int)$foto->orden,
                ];
            } catch (\Throwable $e) {
                $errores[] = $file->name . ': ' . $e->getMessage();
            }
        }

        $sesion->updated_at = time();
        $sesion->save(false);

        return [
            'success' => true,
            'uploaded' => $guardadas,
            'errors' => $errores,
            'uploaded_count' => count($guardadas),
            'error_count' => count($errores),
        ];
    }


    public function actionCitaFotoDeleteAjax()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $data = json_decode(Yii::$app->request->getRawBody(), true);
        $id = (int)($data['id'] ?? 0);
        if (!$id) return ['success' => false, 'message' => 'Falta id.'];

        $foto = Foto::findOne($id);
        if (!$foto) return ['success' => false, 'message' => 'Foto no encontrada.'];

        $abs = Yii::getAlias('@webroot/' . ltrim($foto->archivo, '/'));

        try {
            $sesionId = (int)$foto->foto_sesion_id;
            $foto->delete();
            if (is_file($abs)) @unlink($abs);

            $sesion = FotoSesion::findOne($sesionId);
            if ($sesion) {
                $sesion->updated_at = time();
                $sesion->save(false);
            }

            return ['success' => true];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'No se pudo eliminar.', 'errors' => ['delete' => [$e->getMessage()]]];
        }
    }
}
