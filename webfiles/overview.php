<?php

/**
 * Overview class
 * 
 * DB connection
 * HTML Table of all answers and the corresponding questions, subcategories and categories
 */
class Overview
{
    private $DB;

    /**
     * Constructor for a new questionnaire
     */
    function __construct()
    {
        $this->DB = include 'DB.php';
    }

    /**
     * Create a table, which contains all the answers (and if the answer is correct)
     * and the corresponding questions, subcategories and categories
     * 
     * @return string HTML table
     */
    function createTable()
    {
        $result = '<table>';

        // column names
        // is_true column -> invisible class -> CSS
        $result .= '<tr>
            <th>Kategorie</th>
            <th>Unterkategorie</th>
            <th>Frage</th>
            <th>Antwort</th>
            <th class="invisible is_true">Richtig?</th>
        </tr>';

        try {
            // get all the answers, questions, subcategories, categories, is_true
            $categoryStmt = $this->DB->prepare("SELECT category_text, subcategory_text, question_text, answer_text, is_true
                FROM answer 
                INNER JOIN question ON fk_pk_question_id = pk_question_id
                INNER JOIN subcategory ON fk_pk_subcategory_id = pk_subcategory_id
                INNER JOIN category ON fk_pk_category_id = pk_category_id");
            if ($categoryStmt->execute()) {
                while ($row = $categoryStmt->fetch()) {
                    // for each answer add a new table row
                    $result .= '<tr>';
                    $result .= '<td>' . $row['category_text'] . '</td>';
                    $result .= '<td>' . $row['subcategory_text'] . '</td>';
                    $result .= '<td>' . $row['question_text'] . '</td>';
                    $result .= '<td>' . $row['answer_text'] . '</td>';
                    $result .= '<td class="invisible is_true">' . $row['is_true'] . '</td>';
                    $result .= '</tr>';
                }
            }

            $this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException  $e) {
            echo "Error: " . $e;
            exit();
        }

        return $result . '</table>';
    }
}

?>


<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Quiz Übersicht - Krikler</title>
</head>

<body>
    <?php
    // initialize a new Overview object
    $overview = new Overview();
    ?>

    <?php include 'NAV.html'; ?>


    <div id="main">
        <div class="mainElement">
            <h1>Quiz Übersicht</h1>
        </div>

        <div class="mainElement buttonDiv">
            <!-- only display if an answer is correct, if the button has been pressed -->
            <button id="showIsTrue">Lösungen anzeigen</button>
        </div>

        <div class="mainElement" id="overview">
            <?php
            echo $overview->createTable();
            ?>
        </div>
    </div>

</body>

<script>
    // get all the elements that have the class name "is_true"
    // -> all displays if the answer is true or not
    let isTrue = document.getElementsByClassName("is_true");

    // get the button, that activates / deactivates the display of the "is_true" class elements
    let showIsTrue = document.getElementById("showIsTrue");

    // boolean value, which if true if the "is_true" class elements are shown
    let isTrueVisible = false;

    // add an onclick event listener to the showIsTrue button
    showIsTrue.addEventListener("click", function() {
        if (isTrueVisible) {
            // if the value is true -> make all "is_true" elements invisible
            for (let i = 0; i < isTrue.length; i++) {
                isTrue[i].classList.add("invisible");
            }
            isTrueVisible = false;
        } else {
            // if the value is false -> make all "is_true" elements visible
            for (let i = 0; i < isTrue.length; i++) {
                isTrue[i].classList.remove("invisible");
            }
            isTrueVisible = true;
        }
    });
</script>

</html>