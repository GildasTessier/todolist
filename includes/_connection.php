<?php if(!isset($_SESSION['id_user']) || $_SESSION['id_user'] === null) { ?>

<div class="background">
    <form class ="connection" method="post" action="action.php" id="form-connection">
        <p>Connection</p>
        <div>
            <label for="email" >E-mail</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div>
            <label for="password" >Password</label>
            <input type="password" name="password" id="password" required> 
        </div>
        <input type="hidden" name="action" value="connect-account">
        <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
        <input class="submit" type="submit" value="CONNECTION">
        <a href="#" class="js-change-form" >Create an account</a>
    </form>

    <form class ="create-account hidden" action="action.php" method="post" id="form-create-account">
        <p>Create account</p>
        <div>
            <label for="email" >E-mail</label>
            <input type="email" name="new-email" id="new-email" " required>
        </div>
        <div>
            <label for="password" >Password</label>
            <input id="password" name="new-password" type="password" pattern="^\S{6,}$" onchange="this.setCustomValidity(this.validity.patternMismatch ? 'Need minimum 6 characters' : ''); if(this.checkValidity()) form.password_two.pattern = this.value;"  required> 
        </div>
        <div>
            <label for="password" >Confirm password</label>
            <input id="password_two" name="password_two" type="password" pattern="^\S{6,}$" onchange="this.setCustomValidity(this.validity.patternMismatch ? 'Please enter the same Password as above' : '');"  required> 
        </div>
        <input type="hidden" name="action" value="create-account">
        <input type="hidden" name="token" value="<?=$_SESSION['token']?>">
        <input class="submit" type="submit" value="VALIDATION">
        <a href="#" class="js-change-form">Back to connecting page</a>
    </form>
</div>
<?php }