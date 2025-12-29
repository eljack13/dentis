<?php

namespace app\controllers;

use app\models\Cita;
use app\models\CitaSearch;
use app\models\Paciente;
use app\models\Servicio;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use app\models\FotoSesion;
use app\models\Foto;
use yii\web\UploadedFile;

class CitaController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'update-ajax' => ['POST'],
                    'delete-ajax' => ['POST'],
                    'info-ajax' => ['GET'],
                    'move-ajax' => ['POST'],
                    'catalogos' => ['GET'],
                    'events' => ['GET'],
                    'delete' => ['POST'],
                    'create-ajax' => ['POST'],
                    'get-foto-sesion-ajax' => ['GET'],
                    'fotos-ajax' => ['GET'],
                    'upload-foto-ajax' => ['POST'],
                    'delete-foto-ajax' => ['POST'],
                ],
            ],
        ]);
    }

    public function actionIndex()
    {
        $searchModel = new CitaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    // ✅ Feed para FullCalendar
    public function actionEvents($start = null, $end = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$start || !$end) {
            return [];
        }

        $startDt = date('Y-m-d H:i:s', strtotime($start));
        $endDt   = date('Y-m-d H:i:s', strtotime($end));

        $citas = Cita::find()
            ->andWhere(['>=', 'inicio', $startDt])
            ->andWhere(['<', 'inicio', $endDt])
            ->orderBy(['inicio' => SORT_ASC])
            ->all();

        $events = [];
        foreach ($citas as $c) {
            $events[] = [
                'id' => (string)$c->id,
                'title' => 'Cita #' . $c->id,
                'start' => $c->inicio,
                'end'   => $c->fin,
                'extendedProps' => [
                    'paciente_id' => $c->paciente_id ?? null,
                    'servicio_id' => $c->servicio_id ?? null,
                    'notas'       => $c->notas ?? null,
                ],
            ];
        }

        return $events;
    }


    public function actionMoveAjax()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $data = json_decode(Yii::$app->request->getRawBody(), true);
        if (!$data) {
            throw new BadRequestHttpException('JSON inválido.');
        }

        $id = $data['id'] ?? null;
        $start = $data['start'] ?? null;
        $end = $data['end'] ?? null;

        if (!$id || !$start) {
            return ['success' => false, 'message' => 'Faltan datos (id/start).'];
        }

        $model = \app\models\Cita::findOne((int)$id);
        if (!$model) {
            return ['success' => false, 'message' => 'Cita no encontrada.'];
        }
        $model->inicio = date('Y-m-d H:i:s', strtotime($start));
        if ($end) {
            $model->fin = date('Y-m-d H:i:s', strtotime($end));
        }
        // Si tu modelo requiere updated_at:
        if ($model->hasAttribute('updated_at')) {
            $model->updated_at = time();
        }

        if ($model->save()) {
            return ['success' => true];
        }

        return ['success' => false, 'message' => 'No se pudo guardar.', 'errors' => $model->errors];
    }

    public function actionCreateAjax()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $data = json_decode(Yii::$app->request->getRawBody(), true);

            if (!is_array($data)) {
                return ['success' => false, 'message' => 'Body inválido (JSON requerido).'];
            }

            $inicioIn    = $data['inicio'] ?? null;   // datetime-local: 2025-12-28T10:30
            $finIn       = $data['fin'] ?? null;      // opcional
            $pacienteId  = $data['paciente_id'] ?? null;
            $servicioId  = $data['servicio_id'] ?? null;
            $notas       = $data['notas'] ?? null;

            if (!$inicioIn || !$pacienteId || !$servicioId) {
                return ['success' => false, 'message' => 'Faltan datos: inicio, paciente_id, servicio_id.'];
            }

            $paciente = Paciente::findOne((int)$pacienteId);
            if (!$paciente) return ['success' => false, 'message' => 'Paciente no existe.'];

            $servicio = Servicio::findOne((int)$servicioId);
            if (!$servicio) return ['success' => false, 'message' => 'Servicio no existe.'];

            $inicio = date('Y-m-d H:i:s', strtotime($inicioIn));

            if (!empty($finIn)) {
                $fin = date('Y-m-d H:i:s', strtotime($finIn));
            } else {
                $duracion = (int)($servicio->duracion_min ?? 30);
                $buffer   = (int)($servicio->buffer_min ?? 0);
                $fin = date('Y-m-d H:i:s', strtotime($inicio . " +{$duracion} minutes +{$buffer} minutes"));
            }

            $cita = new Cita();
            $cita->paciente_id = (int)$pacienteId;
            $cita->servicio_id = (int)$servicioId;
            $cita->inicio      = $inicio;
            $cita->fin         = $fin;

            if ($cita->hasAttribute('notas')) {
                $cita->notas = $notas;
            }

            $now = time();
            if ($cita->hasAttribute('created_at') && empty($cita->created_at)) {
                $cita->created_at = $now;
            }
            if ($cita->hasAttribute('updated_at')) {
                $cita->updated_at = $now;
            }

            if (!$cita->save()) {
                return ['success' => false, 'message' => 'No se pudo guardar.', 'errors' => $cita->getErrors()];
            }

            return ['success' => true, 'id' => $cita->id];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Error servidor: ' . $e->getMessage()];
        }
    }


    public function actionCatalogos()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $pacientes = \app\models\Paciente::find()
            ->select(['id', 'nombre'])
            ->orderBy(['nombre' => SORT_ASC])
            ->asArray()
            ->all();

        $servicios = \app\models\Servicio::find()
            ->select(['id', 'nombre', 'duracion_min'])
            ->orderBy(['nombre' => SORT_ASC])
            ->asArray()
            ->all();

        return [
            'success' => true,
            'pacientes' => $pacientes,
            'servicios' => $servicios,
        ];
    }


    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new Cita();

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Cita::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionInfoAjax($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $cita = Cita::find()
            ->where(['id' => (int)$id])
            ->one();

        if (!$cita) {
            return ['success' => false, 'message' => 'Cita no encontrada.'];
        }

        $pacienteNombre = null;
        if (!empty($cita->paciente_id)) {
            $p = Paciente::findOne((int)$cita->paciente_id);
            $pacienteNombre = $p ? $p->nombre : null;
        }

        $servicioNombre = null;
        $duracionMin = null;
        if (!empty($cita->servicio_id)) {
            $s = Servicio::findOne((int)$cita->servicio_id);
            if ($s) {
                $servicioNombre = $s->nombre;
                $duracionMin = $s->duracion_min ?? null;
            }
        }

        return [
            'success' => true,
            'cita' => [
                'id' => (string)$cita->id,
                'paciente_id' => $cita->paciente_id,
                'paciente_nombre' => $pacienteNombre,
                'servicio_id' => $cita->servicio_id,
                'servicio_nombre' => $servicioNombre,
                'duracion_min' => $duracionMin,
                'inicio' => $cita->inicio,
                'fin' => $cita->fin,
                'inicio_fmt' => $cita->inicio ? Yii::$app->formatter->asDatetime($cita->inicio, 'php:D d/m/Y H:i') : null,
                'fin_fmt' => $cita->fin ? Yii::$app->formatter->asDatetime($cita->fin, 'php:D d/m/Y H:i') : null,
                'notas' => $cita->notas ?? '',
                'estado' => $cita->estado ?? null,
            ]
        ];
    }

    public function actionDeleteAjax()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $data = json_decode(Yii::$app->request->getRawBody(), true);
        $id = $data['id'] ?? null;

        if (!$id) return ['success' => false, 'message' => 'Falta id.'];

        $model = Cita::findOne((int)$id);
        if (!$model) return ['success' => false, 'message' => 'Cita no encontrada.'];

        try {
            $model->delete();
            return ['success' => true];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'No se pudo eliminar.', 'errors' => ['delete' => [$e->getMessage()]]];
        }
    }

    public function actionUpdateAjax()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $data = json_decode(Yii::$app->request->getRawBody(), true);
        if (!is_array($data)) {
            return ['success' => false, 'message' => 'Body inválido (JSON requerido).'];
        }

        $id         = $data['id'] ?? null;
        $pacienteId = $data['paciente_id'] ?? null;
        $servicioId = $data['servicio_id'] ?? null;
        $inicioIn   = $data['inicio'] ?? null; // viene como datetime-local: 2025-12-28T10:30
        $finIn      = $data['fin'] ?? null;
        $notas      = $data['notas'] ?? null;

        if (!$id || !$pacienteId || !$servicioId || !$inicioIn) {
            return ['success' => false, 'message' => 'Faltan datos: id, paciente_id, servicio_id, inicio.'];
        }

        $model = Cita::findOne((int)$id);
        if (!$model) {
            return ['success' => false, 'message' => 'Cita no encontrada.'];
        }

        $paciente = Paciente::findOne((int)$pacienteId);
        if (!$paciente) return ['success' => false, 'message' => 'Paciente no existe.'];

        $servicio = Servicio::findOne((int)$servicioId);
        if (!$servicio) return ['success' => false, 'message' => 'Servicio no existe.'];

        $inicio = date('Y-m-d H:i:s', strtotime($inicioIn));

        if (!empty($finIn)) {
            $fin = date('Y-m-d H:i:s', strtotime($finIn));
        } else {
            $duracion = (int)($servicio->duracion_min ?? 30);
            $buffer   = (int)($servicio->buffer_min ?? 0);
            $fin = date('Y-m-d H:i:s', strtotime($inicio . " +{$duracion} minutes +{$buffer} minutes"));
        }

        $model->paciente_id = (int)$pacienteId;
        $model->servicio_id = (int)$servicioId;
        $model->inicio      = $inicio;
        $model->fin         = $fin;

        if ($model->hasAttribute('notas')) {
            $model->notas = $notas;
        }
        if ($model->hasAttribute('updated_at')) {
            $model->updated_at = time();
        }

        if (!$model->save()) {
            return ['success' => false, 'message' => 'No se pudo guardar.', 'errors' => $model->getErrors()];
        }

        return ['success' => true];
    }

    public function actionGetFotoSesionAjax($cita_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $cita = Cita::findOne((int)$cita_id);
        if (!$cita) return ['success' => false, 'message' => 'Cita no encontrada.'];

        if (empty($cita->paciente_id)) {
            return ['success' => false, 'message' => 'La cita no tiene paciente asignado.'];
        }

        $sesion = FotoSesion::find()
            ->where(['cita_id' => (int)$cita_id])
            ->one();

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

        return [
            'success' => true,
            'sesion' => [
                'id' => (int)$sesion->id,
                'paciente_id' => (int)$sesion->paciente_id,
                'cita_id' => (int)$sesion->cita_id,
            ]
        ];
    }

    public function actionFotosAjax($cita_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $sesion = FotoSesion::find()
            ->where(['cita_id' => (int)$cita_id])
            ->one();

        if (!$sesion) {
            return ['success' => true, 'fotos' => []];
        }

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

        return ['success' => true, 'fotos' => $fotos, 'foto_sesion_id' => (int)$sesion->id];
    }

    public function actionUploadFotoAjax()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $citaId = (int)Yii::$app->request->post('cita_id');
        if (!$citaId) return ['success' => false, 'message' => 'Falta cita_id.'];

        $cita = Cita::findOne($citaId);
        if (!$cita) return ['success' => false, 'message' => 'Cita no encontrada.'];

        if (empty($cita->paciente_id)) {
            return ['success' => false, 'message' => 'La cita no tiene paciente asignado.'];
        }

        $sesion = FotoSesion::find()->where(['cita_id' => $citaId])->one();
        if (!$sesion) {
            $sesion = new FotoSesion();
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

        $file = UploadedFile::getInstanceByName('foto');
        if (!$file) return ['success' => false, 'message' => 'No se recibió archivo (foto).'];

        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file->type, $allowed, true)) {
            return ['success' => false, 'message' => 'Formato no permitido. Usa JPG/PNG/WEBP.'];
        }

        if ($file->size > 10 * 1024 * 1024) {
            return ['success' => false, 'message' => 'Máximo 10 MB por foto.'];
        }

        $pacienteId = (int)$cita->paciente_id;
        $dirAbs = Yii::getAlias('@webroot/uploads/pacientes/' . $pacienteId . '/citas/' . $citaId);
        if (!is_dir($dirAbs)) @mkdir($dirAbs, 0775, true);

        $ext = strtolower($file->getExtension());
        $safeName = 'cita_' . $citaId . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

        $abs = $dirAbs . DIRECTORY_SEPARATOR . $safeName;
        if (!$file->saveAs($abs)) {
            return ['success' => false, 'message' => 'No se pudo guardar el archivo.'];
        }

        $rel = 'uploads/pacientes/' . $pacienteId . '/citas/' . $citaId . '/' . $safeName;

        $imgW = null;
        $imgH = null;
        $info = @getimagesize($abs);
        if (is_array($info)) {
            $imgW = $info[0] ?? null;
            $imgH = $info[1] ?? null;
        }

        $maxOrden = (int) Foto::find()->where(['foto_sesion_id' => (int)$sesion->id])->max('orden');
        $orden = $maxOrden ? ($maxOrden + 1) : 1;

        $foto = new Foto();
        $foto->foto_sesion_id = (int)$sesion->id;
        $foto->archivo = $rel;
        $foto->mime = $file->type;
        $foto->size_bytes = (int)$file->size;
        $foto->ancho = $imgW;
        $foto->alto = $imgH;
        $foto->etiqueta = Yii::$app->request->post('etiqueta') ?: null;
        $foto->orden = $orden;
        $foto->created_at = time();

        if (!$foto->save()) {
            @unlink($abs);
            return ['success' => false, 'message' => 'No se pudo guardar en BD.', 'errors' => $foto->getErrors()];
        }

        $sesion->updated_at = time();
        $sesion->save(false);

        return [
            'success' => true,
            'foto' => [
                'id' => (int)$foto->id,
                'url' => rtrim(Yii::$app->request->baseUrl, '/') . '/' . ltrim($rel, '/'),
                'archivo' => $rel,
                'orden' => (int)$foto->orden,
                'etiqueta' => $foto->etiqueta,
            ]
        ];
    }

    public function actionDeleteFotoAjax()
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
