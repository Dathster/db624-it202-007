<?php
require(__DIR__ . "/../../partials/nav.php");
?>
<form method="POST" onsubmit="return validate(this);">
    <div>
        <label for="email">Email/Username</label>
        <input type="text" id="email" name="email" required />
    </div>
    <div>
        <label for="pw">Password</label>
        <input type="password" id="pw" name="password" required minlength="8" />
    </div>
    <input type="submit" value="Login" name="login" id="login" />
</form>
<script>
    function validate(form) {
        //TODO 1: implement JavaScript validation
        // ensure it returns false for an error and true for success
        
        let isValid = true;

        let email = form.email.value;
        let pw = form.password.value;
        
        //Ensure email and password fields are not empty
        if(!email){
            flash("[Client]: Email/Username must not be empty", "warning");
            isValid = false;
        }else{
            //Determine whether email is email or username and validate accordingly
            isValid = (email.includes("@"))?validate_email(email):validate_username(email);
        }

        if(!pw){
            flash("[Client]: Password must not be empty", "warning");
            isValid = false;
        }else{
            //Make sure password is valid
            isValid = validate_password(password) && isValid;
        }
        
        return isValid;
    }
</script>
<?php
//TODO 2: add PHP Code
if (isset($_POST["login"]) && isset($_POST["email"]) && isset($_POST["password"])) {
    $email = se($_POST, "email", "", false);
    $password = se($_POST, "password", "", false);

    //TODO 3
    $hasError = false;
    if (empty($email)) {
        flash("Email/Username must not be empty");
        $hasError = true;
    }
    
    if(str_contains($email, "@")){
        //sanitize
        //$email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $email = sanitize_email($email);
        //validate
        /*if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash("Invalid email address");
            $hasError = true;
        }*/
        if (!is_valid_email($email)) {
            flash("Invalid email address");
            $hasError = true;
        }
    }else{
        if(!is_valid_username($email)){
            flash("Invalid username");
            $hasError = true;
        }
    }
    
    if (empty($password)) {
        flash("password must not be empty");
        $hasError = true;
    }
    if (!is_valid_password($password)) {
        flash("Password too short");
        $hasError = true;
    }
    if (!$hasError) {
        //flash("Welcome, $email");
        //TODO 4
        $db = getDB();
        $stmt = $db->prepare("SELECT id, email, username, password from Users 
        where email = :email or username = :email");
        try {
            $r = $stmt->execute([":email" => $email]);
            if ($r) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    $hash = $user["password"];
                    unset($user["password"]);
                    if (password_verify($password, $hash)) {
                        //flash("Weclome $email");
                        $_SESSION["user"] = $user; //sets our session data from db
                        //fetching user roles
                        try{
                            //look up potential roles
                            $stmt = $db->prepare("SELECT Roles.name FROM Roles JOIN UserRoles on Roles.id = UserRoles.role_id where UserRoles.user_id = :user_id and Roles.is_active = 1 and UserRoles.is_active = 1");
                            $stmt->execute([":user_id"=>$user["id"]]);    
                            $roles = $stmt->FetchAll(PDO::FETCH_ASSOC); //fetching all records as multiple roles could be present
                        }catch(Exception $e){
                            error_log(var_export($e, true));
                        }

                        //saving the roles to an array
                        if(isset($roles)){
                            $_SESSION["user"]["roles"] = $roles; //if user has at least one role
                        }else{
                            $_SESSION["user"]["roles"] = []; //empty array if user has no roles
                        }

                        flash("Welcome, " . get_username());
                        die(header("Location: home.php"));
                    } else {
                        flash("Invalid password");
                    }
                } else {
                    flash("Email not found");
                }
            }
        } catch (Exception $e) {
            flash("<pre>" . var_export($e, true) . "</pre>");
        }
    }
}
?>
<?php 
require(__DIR__."/../../partials/flash.php");