<?php
/*put this at the bottom of the page so any templates
 populate the flash variable and then display at the proper timing*/
?>
<div class="container" id="flash">
    <?php $messages = getMessages() ?>
    <?php if ($messages) : ?>
        <?php foreach ($messages as $msg) : ?>
            <div class="row">
                

            <div class="toast-container position-fixed top-1 end-0">
                <div id="liveToast" class="toast bg-<?php se($msg, 'color', 'info'); ?>" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">Steamed Games</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body bg-<?php se($msg, 'color', 'info'); ?>">
                        <?php se($msg, "text", ""); ?>
                    </div>
                </div>
            </div>






            </div>


   


        <?php endforeach; ?>


    <?php endif; ?>
</div>

<script>
    //used to pretend the flash messages are below the first nav element
    function moveMeUp(ele) {
        let target = document.getElementsByTagName("nav")[0];
        if (target) {
            target.after(ele);
        }
    }

    moveMeUp(document.getElementById("flash"));



    document.addEventListener("DOMContentLoaded", function () {
    const toastElements = document.querySelectorAll('.toast');
    toastElements.forEach(toastEl => {
        const toast = new bootstrap.Toast(toastEl); // Initialize toast
        toast.show(); // Show toast
    });
});

</script>
<style>
    .alert-success {
        background-color: green
    }

    .alert-warning {
        background-color: yellow;
    }

    .alert-danger {
        background-color: red;
    }

    .alert-info {
        background-color: teal;
    }
</style>



