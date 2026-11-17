<?php
session_start();

if (!isset($_SESSION['login']))
{
    header("Location: login.php");
    exit();
}

if (isset($_GET['id_ucznia']))
{
    require_once "dbconnect.php";

    $connection = mysqli_connect($host, $user, $password, $dbname);

    $id = $_GET['id_ucznia'];

    $query = "DELETE FROM uczen
              WHERE id='$id'
              AND id_klasy='{$_SESSION['id_klasy']}'";

    mysqli_query($connection, $query);

    mysqli_close($connection);
}

header("Location:index.php");
exit();
?>