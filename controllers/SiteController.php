<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Cita;
use app\models\Paciente;
use app\models\Servicio; // Si tienes modelo de pagos/servicios
use yii\helpers\ArrayHelper;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
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
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
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
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login']);
        }

        // --- 1. ESTADÍSTICAS RÁPIDAS (KPIs) ---

        // Citas de HOY
        $citasHoy = Cita::find()
            ->where(['between', 'inicio', date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])
            ->count();

        // Total de Pacientes Activos
        $totalPacientes = Paciente::find()->count();

        // Citas Pendientes (Por confirmar)
        $citasPendientes = Cita::find()
            ->where(['estado' => 'PENDIENTE']) // Ajusta 'PENDIENTE' a como lo guardes en tu BD
            ->count();

        // Ingresos Estimados del Mes (Ejemplo: Suma de precios de citas de este mes)
        // Nota: Si no tienes columna 'precio' en cita, puedes quitar esto.
        $ingresosMes = Cita::find()
            ->where(['between', 'inicio', date('Y-m-01'), date('Y-m-t')])
            // ->sum('costo_total'); // Descomenta si tienes un campo de costo
            ->count(); // Por ahora contamos citas del mes como placeholder


        // --- 2. DATOS PARA GRÁFICO DE BARRAS (Últimos 7 días) ---
        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $chartLabels[] = date('d/M', strtotime($date)); // Ej: 12/Oct

            $count = Cita::find()
                ->where(['like', 'inicio', $date])
                ->count();
            $chartData[] = $count;
        }

        // --- 3. DATOS PARA GRÁFICO DE DONA (Estados) ---
        $estados = Cita::find()->select('estado, COUNT(*) as cnt')->groupBy('estado')->asArray()->all();
        $donutLabels = ArrayHelper::getColumn($estados, 'estado');
        $donutData = ArrayHelper::getColumn($estados, 'cnt');

        // --- 4. PRÓXIMAS CITAS (Tabla inferior) ---
        $proximasCitas = Cita::find()
            ->where(['>=', 'inicio', date('Y-m-d H:i:s')])
            ->orderBy(['inicio' => SORT_ASC])
            ->limit(5)
            ->all();

        return $this->render('index', [
            'citasHoy' => $citasHoy,
            'totalPacientes' => $totalPacientes,
            'citasPendientes' => $citasPendientes,
            'ingresosMes' => $ingresosMes,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
            'donutLabels' => $donutLabels,
            'donutData' => $donutData,
            'proximasCitas' => $proximasCitas,
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
