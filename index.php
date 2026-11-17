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
    die("Błąd połączenia z bazą danych");
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dziennik szkolny</title>
    <link rel="stylesheet" href="main.css">
</head>

<body>

<form action="logout.php" method="post">
    <input type="submit" value="WYLOGUJ">
</form>

<h2>Witaj, <?php echo htmlspecialchars($_SESSION['login']); ?>!</h2>

<form action="index.php" method="post">

    <select name="klasa" onchange="this.form.submit();">

        <option value="">WYBIERZ KLASĘ</option>

        <option value="1a">1a</option>
        <option value="1b">1b</option>
        <option value="2a">2a</option>
        <option value="2b">2b</option>

    </select>

</form>

<?php

if (isset($_POST['klasa']) && $_POST['klasa'] != "")
{
    $klasa = $_POST['klasa'];
}
else if (isset($_COOKIE['klasa']))
{
    $klasa = $_COOKIE['klasa'];
}
else
{
    $klasa = "";
}

if ($klasa != "")
{
    $klasa_bezpieczna = mysqli_real_escape_string($connection, $klasa);

    $query = "
        SELECT uczen.id, uczen.Imie, uczen.Nazwisko,
        uczen.Srednia_ocen, uczen.id_klasy,
        klasa.nazwa, wychowawca.imie AS imie_wych,
        wychowawca.nazwisko AS nazwisko_wych
        FROM uczen
        INNER JOIN klasa ON klasa.id = uczen.id_klasy
        INNER JOIN wychowawca ON klasa.id = wychowawca.id_klasy
        WHERE klasa.nazwa = '$klasa_bezpieczna'
    ";

    $result = mysqli_query($connection, $query);

    $row2 = mysqli_fetch_assoc($result);

    if (!$row2)
    {
        echo '<p style="color:red;">Nie ma takiej klasy w szkole!</p>';
    }
    else
    {
        $imie_wych = $row2['imie_wych'];
        $nazwisko_wych = $row2['nazwisko_wych'];

        echo "<h3>Klasa $klasa - $imie_wych $nazwisko_wych</h3>";

        echo "<table>";

        echo "<tr>
                <th>Imię</th>
                <th>Nazwisko</th>
                <th>Średnia ocen</th>";

        
        if ($_SESSION['id_klasy'] == $row2['id_klasy'])
        {
            echo "<th>EDYTUJ</th>";
            echo "<th>KASUJ</th>";
        }

        echo "</tr>";

        $srednia_ocen = 0;
        $liczba = 0;

        
        do
        {
            echo "<tr>";

            echo "<td>".$row2['Imie']."</td>";
            echo "<td>".$row2['Nazwisko']."</td>";
            echo "<td>".$row2['Srednia_ocen']."</td>";

            if ($_SESSION['id_klasy'] == $row2['id_klasy'])
            {
                echo '<td>
                    <button onclick="window.location.href=\'edytuj_ucznia.php?id_ucznia='.$row2['id'].'\'">
                    EDYTUJ
                    </button>
                </td>';

                echo '<td>
                    <button onclick="window.location.href=\'usun_ucznia.php?id_ucznia='.$row2['id'].'\'">
                    KASUJ
                    </button>
                </td>';
            }

            echo "</tr>";

            $srednia_ocen += $row2['Srednia_ocen'];
            $liczba++;

        }
        while ($row2 = mysqli_fetch_assoc($result));

        echo "</table>";

        echo "<p>Średnia ocen klasy: ".
             round($srednia_ocen / $liczba, 2).
             "</p>";

       
        if ($_SESSION['id_klasy'] == $_SESSION['id_klasy'])
        {
            $query_id = "SELECT id FROM klasa WHERE nazwa='$klasa_bezpieczna'";
            $result_id = mysqli_query($connection, $query_id);
            $row_id = mysqli_fetch_assoc($result_id);

            if ($_SESSION['id_klasy'] == $row_id['id'])
            {
                echo "<h3>DODAWANIE NOWEGO UCZNIA</h3>";

                echo <<<END
                <form action="dodaj_ucznia.php" method="post">

                    <input type="text" name="imie"
                    placeholder="IMIĘ" required><br>

                    <input type="text" name="nazwisko"
                    placeholder="NAZWISKO" required><br>

                    <input type="number" step="0.01"
                    name="srednia_ocen"
                    placeholder="ŚREDNIA OCEN" required><br>

                    <input type="hidden" name="id_klasy"
                    value="{$row_id['id']}">

                    <input type="submit" name="dodaj_ucznia"
                    value="ZAPISZ">

                </form>
END;
            }
        }
    }

    setcookie("klasa", $klasa, time() + 3600);
}

mysqli_close($connection);

?>

</body>
</html>