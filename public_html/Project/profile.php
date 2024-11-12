
<?php //db624 it202-007 11/11/24
require_once(__DIR__ . "/../../partials/nav.php");
    is_logged_in(true);
?>
<?php
if (isset($_POST["save"])) {
    //Variable to track if all update operations went successfully
    $success = true;

    $email = se($_POST, "email", null, false);
    $username = se($_POST, "username", null, false);

    $params = [":email" => $email, ":username" => $username, ":id" => get_user_id()];
    $db = getDB();
    $stmt = $db->prepare("UPDATE Users set email = :email, username = :username where id = :id");
    try {
        $stmt->execute($params);
        //flash("Profile saved", "success");
    } catch (PDOException $e) {
        if ($e->errorInfo[1] === 1062) {
            //https://www.php.net/manual/en/function.preg-match.php
            preg_match("/Users.(\w+)/", $e->errorInfo[2], $matches);
            if (isset($matches[1])) {
                flash("The chosen " . $matches[1] . " is not available.", "warning");
                $success = false;
            } else {
                $error = $e->errorInfo[2];
                flash("An unexpected error has occured. $error", "danger");
                //echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
                $success = false;
            }
        } else {
            //TODO come up with a nice error message
                $error = $e->errorInfo[1];
                flash("An unexpected error has occured. Code $error", "danger");
            echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
            $success = false;
        }
    } catch (Exception $e){
        flash("An unexpected error has occured, please try again.", "danger");
        $success = false;
    }
    //select fresh data from table
    $stmt = $db->prepare("SELECT id, email, username from Users where id = :id LIMIT 1");
    try {
        $stmt->execute([":id" => get_user_id()]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            //$_SESSION["user"] = $user;
            $_SESSION["user"]["email"] = $user["email"];
            $_SESSION["user"]["username"] = $user["username"];
        } else {
            flash("User doesn't exist", "danger");
            $success = false;
        }
    } catch (Exception $e) {
        flash("An unexpected error occurred, please try again", "danger");
        //echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
        $success = false;
    }


    //check/update password
    $current_password = se($_POST, "currentPassword", null, false);
    $new_password = se($_POST, "newPassword", null, false);
    $confirm_password = se($_POST, "confirmPassword", null, false);
    if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
        if ($new_password === $confirm_password) {
            //TODO validate current
            $stmt = $db->prepare("SELECT password from Users where id = :id");
            try {
                $stmt->execute([":id" => get_user_id()]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if (isset($result["password"])) {
                    if (password_verify($current_password, $result["password"])) {
                        $query = "UPDATE Users set password = :password where id = :id";
                        $stmt = $db->prepare($query);
                        $stmt->execute([
                            ":id" => get_user_id(),
                            ":password" => password_hash($new_password, PASSWORD_BCRYPT)
                        ]);

                        flash("Password reset", "success");
                    } else {
                        flash("Current password is invalid", "warning");
                        $success = false;
                    }
                }
            } catch (PDOException $e) {
                echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
                $success = false;
            } catch (Exception $e){
                echo "<pre> An error occured </pre>";
                $success = false;
            }
        } else {
            flash("New passwords don't match", "warning");
            $success = false;
        }
    }
    if($success){
        flash("Profile saved", "success");
    }
}
?>

<?php
$email = get_user_email();
$username = get_username();
?>
<h1>Update profile</h1>
<form method="POST" onsubmit="return validate(this);">
    <div class="mb-3">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?php se($email); ?>" />
    </div>
    <div class="mb-3">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" value="<?php se($username); ?>" />
    </div>
    <!-- DO NOT PRELOAD PASSWORD -->
    <h2>Password Reset</h2>
    <div class="mb-3">
        <label for="cp">Current Password</label>
        <input type="password" name="currentPassword" id="cp" />
    </div>
    <div class="mb-3">
        <label for="np">New Password</label>
        <input type="password" name="newPassword" id="np" />
    </div>
    <div class="mb-3">
        <label for="conp">Confirm Password</label>
        <input type="password" name="confirmPassword" id="conp" />
    </div>
    <input type="submit" value="Update Profile" name="save" />
</form>

<script>
    function validate(form) {
        let pw = form.newPassword.value;
        let con = form.confirmPassword.value;
        let usr = form.username.value;
        let email = form.email.value;
        let currPw = form.currentPassword.value;
        let isValid = true;

        //example of using flash via javascript
        //find the flash container, create a new element, appendChild
        if (pw !== con) {
            flash("[Client]: Password and Confrim password must match", "warning");
            isValid = false;
        }

        //Validate username, new password, and email
        isValid = (pw)?validate_password(pw) && isValid: isValid;
        isValid = validate_username(usr) && isValid;
        isValid = validate_email(email) && isValid;

        //Check if user is trying to update passwords without entering current password
        if(currPw.length == 0 && (pw && con)){
            flash("[Client]: Must enter current password before updating password", "warning");
            isValid = false;
        }

        return isValid;
    }
</script>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>