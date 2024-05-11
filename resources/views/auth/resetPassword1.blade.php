<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="text/css" href="/css/base.css" rel="stylesheet">
    <link type="text/css" href="/css/main.css" rel="stylesheet">
    <title>Document</title>
</head>
<body>
    <!-- modal -->
    <div class="modal">
            <div class="modal__overlay"></div>

            <div class="modal__body">

                <!-- LogIn form -->
             
                    <div class="auth-form__container">
                        <div class="auth-form__header">
                            <h3 class="auth-form__heading">Đổi mật khẩu</h3>
                        </div>

                       <form action="{{ route('formResetPassword1')}}" method="post">
                       @csrf 
                       <div class="auth-form__form">
                            <div class="auth-form__group">
                                <input type="text" class="auth-form__input" placeholder="Email" id="email" class="form-control" name="email">
                            </div>
                            <div class="auth-form__group">
                                <input type="password" class="auth-form__input" placeholder="Password" id="password" class="form-control" name="password">
                            </div>
                        </div>   
                        <label for="seller">Người Bán</label>
                        <input id="seller" name="role" value="seller" type="radio">

                        <label for="customer">Người Mua</label>
                        <input id="customer" name="role" value="customer" type="radio">

                        <label for="customer">Quản Trị Viên</label>
                        <input id="customer" name="role" value="admin" type="radio">
                        <div class="auth-form__aside">  
                        </div>
                        
                        <div class="auth-form__controls">
                        <button type="submit" class="btn btn--primary">XÁC NHẬN</button>
                        </form>
                        </div>
                    </div>

                    <div class="auth-form__socials">
                    </div>
              
            </div>
        </div>
</body>
</html>