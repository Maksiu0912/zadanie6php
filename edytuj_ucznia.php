<?php
session_start();

if (!isset($_SESSION['login']))
{
    header("Location: login.php");
    exit();
}

require_once "dbconnect.php";

$connection = mysqli_connect($host, $user, $password, $dbname);

if (!$connection)
{
    die("Błąd połączenia!");
}


if (isset($_POST['zapisz_edycja']))
{
    $id = $_POST['id_ucznia'];
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $srednia_ocen = $_POST['srednia_ocen'];

    $query = "UPDATE uczen
              SET Imie='$imie',
                  Nazwisko='$nazwisko',
                  Srednia_ocen='$srednia_ocen'
              WHERE id='$id'
              AND id_klasy='{$_SESSION['id_klasy']}'";

    mysqli_query($connection, $query);

    header("Location:index.php");
    exit();
}


if (isset($_GET['id_ucznia']))
{
    $id = $_GET['id_ucznia'];

    $query = "SELECT * FROM uczen
              WHERE id='$id'
              AND id_klasy='{$_SESSION['id_klasy']}'";

    $result = mysqli_query($connection, $query);

    $row = mysqli_fetch_assoc($result);

    if (!$row)
    {
        die("Nie masz uprawnień do edycji tego ucznia!");
    }

    $imie = $row['Imie'];
    $nazwisko = $row['Nazwisko'];
    $srednia_ocen = $row['Srednia_ocen'];

    echo <<<END

    <form method="post">

        <input type="text" name="imie"
        value="$imie" required><br>

        <input type="text" name="nazwisko"
        value="$nazwisko" required><br>

        <input type="number" step="0.01"
        name="srednia_ocen"
        value="$srednia_ocen" required><br>

        <input type="hidden" name="id_ucznia"
        value="$id">

        <input type="submit" name="zapisz_edycja"
        value="ZAPISZ">

    </form>

END;
}

mysqli_close($connection);
?>