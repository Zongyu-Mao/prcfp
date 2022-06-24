@component('mail::message')
# Email Verify

Please click the button below to verify your email address.<br>
[请点击下方按钮确认并验证您在极参的账号邮箱地址。]

@component('mail::button', ['url' => 'http://localhost:4200/email-verify?token='.$token])
Email Verify[邮箱确认]
@endcomponent

Thanks[感谢使用],<br>
{{ config('app.name') }}[极参]
@endcomponent
