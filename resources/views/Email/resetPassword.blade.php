@component('mail::message')
# 本邮件用于重新设置你的账户密码

请点击下方链接，重置你的极参账户密码.

@component('mail::button', ['url' => 'http://localhost:4200/change-password?token='.$token])
账户密码重置
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
