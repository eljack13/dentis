<?php

namespace app\assets;

use yii\web\AssetBundle;

class NpmAsset extends AssetBundle
{
    public $sourcePath = '@app/node_modules';
    public $js = [
        //'chart.js/auto/auto.js', // Ruta al archivo JS de la librería instalada
        'chart.js/dist/chart.umd.js', // Ruta al archivo UMD de Chart.js
        'aos/dist/aos.js',
        'sweetalert2/dist/sweetalert2.min.js',
        'axios/dist/axios.min.js',
        'izitoast/dist/js/iziToast.min.js',
        'html2canvas/dist/html2canvas.min.js',
        'jspdf/dist/jspdf.umd.min.js',
        'air-datepicker/air-datepicker.js',
        '//cdn.jsdelivr.net/npm/air-datepicker@3.5.3/locale/air-datepicker.es.min.js',
        'moment/min/moment.min.js',
    ];
    public $css = [
        'aos/dist/aos.css',
        'sweetalert2/dist/sweetalert2.min.css',
        'izitoast/dist/css/iziToast.min.css',
        'bootstrap-icons/font/bootstrap-icons.min.css',
        'animate.css/animate.min.css',
        'air-datepicker/air-datepicker.css',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
