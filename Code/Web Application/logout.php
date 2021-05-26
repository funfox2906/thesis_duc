<?php
session_start();
session_destroy();
?>
<html>
    <h1>You have been logged out!</h1>
    <h2>Redirect to Login page...</h2>
</html>
<script>setTimeout(function() { location.replace("index.php")},2000);</script>