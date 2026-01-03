<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

/** @var yii\web\View $this */
/** @var app\models\LoginForm $model */

$this->title = 'Iniciar sesión';

/**
 * Requiere Bootstrap Icons (ya lo traes en layout, pero por si acaso)
 * $this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css');
 */

$this->registerCss(<<<CSS
:root{
  --card-radius: 22px;
  --soft-shadow: 0 18px 55px rgba(2, 8, 23, .12);
  --soft-border: 1px solid rgba(15, 23, 42, .10);
  --focus-ring: 0 0 0 4px rgba(59, 130, 246, .16);
}

.login-wrap{
  min-height: calc(100vh - 120px);
  display:flex;
  align-items:center;
  justify-content:center;
  padding: 28px 16px;
}

.login-card{
  width: 100%;
  max-width: 440px;
  background: #fff;
  border-radius: var(--card-radius);
  box-shadow: var(--soft-shadow);
  border: var(--soft-border);
  overflow:hidden;
}

.login-card__top{
  padding: 22px 22px 16px 22px;
  background: linear-gradient(135deg, rgba(59,130,246,.10) 0%, rgba(16,185,129,.08) 60%, rgba(255,255,255,1) 100%);
}

.brand-row{
  display:flex;
  align-items:center;
  gap: 12px;
}

.brand-badge{
  width: 46px;
  height: 46px;
  border-radius: 14px;
  display:flex;
  align-items:center;
  justify-content:center;
  background: rgba(59,130,246,.12);
  border: 1px solid rgba(59,130,246,.18);
}

.brand-badge i{
  font-size: 20px;
  color: #2563eb;
}

.login-title{
  margin:0;
  font-weight: 900;
  letter-spacing: -.02em;
  color:#0f172a;
  line-height: 1.15;
}

.login-subtitle{
  margin: 6px 0 0;
  color:#64748b;
  font-size: .95rem;
}

.login-card__body{
  padding: 18px 22px 22px 22px;
}

.form-label{
  font-weight: 800;
  color:#0f172a;
}

.form-control{
  border-radius: 14px;
  padding: 12px 12px;
  border: 1px solid rgba(15, 23, 42, .14);
}

.form-control:focus{
  box-shadow: var(--focus-ring);
  border-color: rgba(59,130,246,.55);
}

.input-group-text{
  border-radius: 14px;
  border: 1px solid rgba(15, 23, 42, .14);
  background: rgba(15, 23, 42, .03);
  color: #334155;
}

.input-group .form-control{
  border-left: 0;
}

.input-group .form-control:focus{
  border-left: 0;
}

.btn-login{
  border-radius: 14px;
  padding: 12px 14px;
  font-weight: 900;
  letter-spacing: -.01em;
  box-shadow: 0 10px 22px rgba(37, 99, 235, .18);
}

.btn-login:active{
  transform: translateY(1px);
}

.remember-row{
  display:flex;
  justify-content:space-between;
  align-items:center;
  gap: 12px;
  margin-top: 6px;
}

.form-check-input{
  width: 1.15em;
  height: 1.15em;
  border-radius: 6px;
}

.form-check-label{
  font-weight: 700;
  color:#334155;
}

.login-foot{
  margin-top: 14px;
  color:#94a3b8;
  font-size: .85rem;
  text-align:center;
}

.alert{
  border-radius: 14px;
}
CSS);

$this->registerJs(<<<JS
// Toggle password show/hide
document.addEventListener('click', function(e){
  const btn = e.target.closest('[data-toggle-pass]');
  if(!btn) return;
  const input = document.getElementById(btn.getAttribute('data-toggle-pass'));
  if(!input) return;
  const isPwd = input.getAttribute('type') === 'password';
  input.setAttribute('type', isPwd ? 'text' : 'password');
  btn.innerHTML = isPwd ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
});
JS, \yii\web\View::POS_END);
?>

<div class="login-wrap">
    <div class="login-card">
        <div class="login-card__top">
            <div class="brand-row">
                <div class="brand-badge" aria-hidden="true">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <div>
                    <h2 class="login-title"><?= Html::encode($this->title) ?></h2>
                    <p class="login-subtitle">Acceso al sistema.</p>
                </div>
            </div>
        </div>

        <div class="login-card__body">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <!-- Email -->
            <?= $form->field($model, 'email', [
                'template' => "{label}\n<div class=\"input-group\">"
                    . "<span class=\"input-group-text\"><i class=\"bi bi-envelope\"></i></span>\n{input}</div>\n{error}\n{hint}",
            ])->textInput([
                'autofocus' => true,
                'placeholder' => 'correo@dominio.com',
                'autocomplete' => 'email',
                'class' => 'form-control',
            ])->label('Correo') ?>

            <!-- Password -->
            <?= $form->field($model, 'password', [
                'template' => "{label}\n<div class=\"input-group\">"
                    . "<span class=\"input-group-text\"><i class=\"bi bi-key\"></i></span>\n{input}"
                    . "<button class=\"btn btn-outline-secondary\" type=\"button\" style=\"border-radius:14px;\" data-toggle-pass=\"login-password\"><i class=\"bi bi-eye\"></i></button>"
                    . "</div>\n{error}\n{hint}",
            ])->passwordInput([
                'id' => 'login-password',
                'placeholder' => '********',
                'autocomplete' => 'current-password',
                'class' => 'form-control',
            ])->label('Contraseña') ?>

            <div class="remember-row">
                <div>
                    <?= $form->field($model, 'rememberMe')->checkbox([
                        'class' => 'form-check-input',
                    ])->label('Recordarme', ['class' => 'form-check-label']) ?>
                </div>

                <?php // Si luego agregas recuperación, aquí queda el enlace 
                ?>
                <a href="javascript:void(0)" class="text-decoration-none small" style="color:#2563eb;font-weight:800;" onclick="return false;">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>

            <div class="mt-3">
                <?= Html::submitButton(
                    '<i class="bi bi-box-arrow-in-right me-2"></i>Entrar',
                    ['class' => 'btn btn-primary w-100 btn-login', 'name' => 'login-button']
                ) ?>
            </div>

            <?php ActiveForm::end(); ?>

            <div class="login-foot">
                © Theeth Care <?= date('Y') ?> • Seguridad y gestión clínica
            </div>
        </div>
    </div>
</div>