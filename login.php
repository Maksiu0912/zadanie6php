<?php
session_start();

if (isset($_POST['zaloguj']))
{
    require_once "dbconnect.php";

    $connection = mysqli_connect($host, $user, $password, $dbname);

    if (!$connection)
    {
        die("Błąd połączenia z bazą danych");
    }

    $login = $_POST['login'];
    $haslo = $_POST['haslo'];

    $query = "SELECT * FROM wychowawca
              WHERE imie='$login'
              AND nazwisko='$haslo'";

    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) > 0)
    {
        $row = mysqli_fetch_assoc($result);

        $_SESSION['login'] = $row['imie'];
        $_SESSION['haslo'] = $row['nazwisko'];
        $_SESSION['id_klasy'] = $row['id_klasy'];

        header("Location: index.php");
        exit();
    }
    else
    {
        $blad = "ZŁE DANE LOGOWANIA";
    }

    mysqli_close($connection);
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
    <link rel="stylesheet" href="main.css">
</head>

<body>

<h2>Logowanie</h2>

<form method="post">

    <input type="text" name="login"
           placeholder="LOGIN" required><br>

    <input type="password" name="haslo"
           placeholder="HASŁO" required><br>

    <input type="submit" name="zaloguj"
           value="ZALOGUJ">

</form>

<?php
if (isset($blad))
{
    echo "<p style='color:red;'>$blad</p>";
}
?>

</body>
</html>