<?php

/**
 * Admin class
 * 
 * DB connection
 * Insert, Delete, Alter the values of the Quiz DB
 */
class Admin
{
    private $DB;
    private $method = "get";

    /**
     * Constructor for a new questionnaire
     */
    function __construct()
    {
        $this->DB = include 'DB.php';

        if (isset($_GET['insert'])) {
            // if something has to be inserted into the DB -> check, which text is set

            if (isset($_GET['category_text'])) {
                // insert into category table -> category_text
                $this->insertIntoCategory($_GET['category_text']);
            } else if (isset($_GET['subcategory_text']) && isset($_GET['category'])) {
                // insert into subcategory table -> category_id; subcategory_text
                $this->insertIntoSubcategory($_GET['category'], $_GET['subcategory_text']);
            } else if (isset($_GET['question_text']) && isset($_GET['subcategory'])) {
                // insert into question table -> question_id; question_text
                $this->insertIntoQuestion($_GET['subcategory'], $_GET['question_text']);
            } else if (isset($_GET['answer_text']) && isset($_GET['question'])) {
                // insert into answer table -> answer_id; question_text; is_true (checkbox)
                $this->insertIntoAnswer($_GET['question'], $_GET['answer_text'], isset($_GET['is_true']));
            }
        } else if (isset($_GET['delete'])) {
            // if something has to be deleted from the DB -> check, which text is set

            if (isset($_GET['category'])) {
                // delete from category table; category_id
                $this->deleteCategory($_GET['category']);
            } else if (isset($_GET['subcategory'])) {
                // delete from subcategory table; subcategory_id
                $this->deleteSubcategory($_GET['subcategory']);
            } else if (isset($_GET['question'])) {
                // delete from question table; question_id
                $this->deleteQuestion($_GET['question']);
            } else if (isset($_GET['answer'])) {
                // delete from answer table; answer_id
                $this->deleteAnswer($_GET['answer']);
            }
        } else if (isset($_GET['update'])) {
            // if something has to be altered from the DB -> check, which text is set

            if (isset($_GET['category']) && isset($_GET['category_text'])) {
                // update from category table; category_id; category_text
                $this->updateCategory($_GET['category'], $_GET['category_text']);
            } else if (isset($_GET['subcategory']) && isset($_GET['subcategory_text'])) {
                // update from subcategory table; subcategory_id; subcategory_text
                $this->updateSubcategory($_GET['subcategory'], $_GET['subcategory_text']);
            } else if (isset($_GET['question']) && isset($_GET['question_text'])) {
                // update from question table; question_id; question_text
                $this->updateQuestion($_GET['question'], $_GET['question_text']);
            } else if (isset($_GET['answer']) && isset($_GET['answer_text'])) {
                // update from answer table; answer_text_id; answer_text; is_true
                $this->updateAnswer($_GET['answer'], $_GET['answer_text'], isset($_GET['is_true']));
            }
        }

        // if insert or delete or update is set -> redirect back to the admin page
        // in order to prevent multiple inserts, when reloading the page
        if (isset($_GET['insert']) || isset($_GET['delete']) || isset($_GET['update'])) {
            header('Location: admin.php');
        }
    }

    /**
     * Getter: get the method used, when submitting an HTML form
     * 
     * @return string method get or post
     */
    function getMethod()
    {
        return $this->method;
    }


    /**
     * Insert a new entry into the category table
     * 
     * @param int       $pk_category_id category id of the new category
     * @param string    $category_text  category text of the new category
     */
    function insertIntoCategory($category_text)
    {
        try {
            $stmt = $this->DB->prepare("INSERT INTO category (category_text) VALUES (:category_text)");
            $stmt->bindParam(':category_text', $category_text, PDO::PARAM_STR);
            $stmt->execute();
            $this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException  $e) {
            echo "Error: " . $e;
            exit();
        }
    }

    /**
     * Insert a new entry into the subcategory table
     * 
     * @param int       $pk_subcategory_id  subcategory id of the new subcategory
     * @param int       $fk_pk_category_id  category id to which the new subcategory belongs to
     * @param string    $subcategory_text   subcategory text of the new subcategory
     */
    function insertIntoSubcategory($fk_pk_category_id, $subcategory_text)
    {
        try {
            $stmt = $this->DB->prepare("INSERT INTO subcategory (fk_pk_category_id, subcategory_text) VALUES (:fk_pk_category_id, :subcategory_text)");
            $stmt->bindParam(':fk_pk_category_id', $fk_pk_category_id, PDO::PARAM_INT);
            $stmt->bindParam(':subcategory_text', $subcategory_text, PDO::PARAM_STR);
            $stmt->execute();
            $this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException  $e) {
            echo "Error: " . $e;
            exit();
        }
    }

    /**
     * Insert a new entry into the question table
     * 
     * @param int       $pk_question_id         question id of the new question
     * @param int       $fk_pk_subcategory_id   category id to which the new question belongs to
     * @param string    $question_text          question text of the new question
     */
    function insertIntoQuestion($fk_pk_subcategory_id, $question_text)
    {
        try {
            $stmt = $this->DB->prepare("INSERT INTO question (fk_pk_subcategory_id, question_text) VALUES (:fk_pk_subcategory_id, :question_text)");
            $stmt->bindParam(':fk_pk_subcategory_id', $fk_pk_subcategory_id, PDO::PARAM_INT);
            $stmt->bindParam(':question_text', $question_text, PDO::PARAM_STR);
            $stmt->execute();
            $this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException  $e) {
            echo "Error: " . $e;
            exit();
        }
    }

    /**
     * Insert a new entry into the answer table
     * 
     * @param int       $pk_answer_id       answer id of the new answer
     * @param int       $fk_pk_question_id  question id to which the new answer belongs to
     * @param string    $answer_text        answer text of the new answer
     * @param boolean   $is_true            true if the answer is correct
     */
    function insertIntoAnswer($fk_pk_question_id, $answer_text, $is_true)
    {
        try {
            $stmt = $this->DB->prepare("INSERT INTO answer (fk_pk_question_id, is_true, answer_text) VALUES (:fk_pk_question_id, :is_true, :answer_text)");
            $stmt->bindParam(':fk_pk_question_id', $fk_pk_question_id, PDO::PARAM_INT);
            $stmt->bindParam(':is_true', $is_true, PDO::PARAM_BOOL);
            $stmt->bindParam(':answer_text', $answer_text, PDO::PARAM_STR);
            $stmt->execute();
            $this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException  $e) {
            echo "Error: " . $e;
            exit();
        }
    }


    /**
     * Delete an entry from the category table
     * 
     * @param int $pk_category_id category id of the entry, that will be deleted
     */
    function deleteCategory($pk_category_id)
    {
        try {
            $stmt = $this->DB->prepare("DELETE FROM category WHERE pk_category_id = :pk_category_id");
            $stmt->bindParam(':pk_category_id', $pk_category_id, PDO::PARAM_INT);
            $stmt->execute();
            $this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException  $e) {
            echo "Error: " . $e;
            exit();
        }
    }

    /**
     * Delete an entry from the subcategory table
     * 
     * @param int $pk_subcategory_id subcategory id of the entry, that will be deleted
     */
    function deleteSubcategory($pk_subcategory_id)
    {
        try {
            $stmt = $this->DB->prepare("DELETE FROM subcategory WHERE pk_subcategory_id = :pk_subcategory_id");
            $stmt->bindParam(':pk_subcategory_id', $pk_subcategory_id, PDO::PARAM_INT);
            $stmt->execute();
            $this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException  $e) {
            echo "Error: " . $e;
            exit();
        }
    }

    /**
     * Delete an entry from the question table
     * 
     * @param int $pk_question_id question id of the entry, that will be deleted
     */
    function deleteQuestion($pk_question_id)
    {
        try {
            $stmt = $this->DB->prepare("DELETE FROM question WHERE pk_question_id = :pk_question_id");
            $stmt->bindParam(':pk_question_id', $pk_question_id, PDO::PARAM_INT);
            $stmt->execute();
            $this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException  $e) {
            echo "Error: " . $e;
            exit();
        }
    }

    /**
     * Delete an entry from the answer table
     * 
     * @param int $pk_answer_id answer id of the entry, that will be deleted
     */
    function deleteAnswer($pk_answer_id)
    {
        try {
            $stmt = $this->DB->prepare("DELETE FROM answer WHERE pk_answer_id = :pk_answer_id");
            $stmt->bindParam(':pk_answer_id', $pk_answer_id, PDO::PARAM_INT);
            $stmt->execute();
            $this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException  $e) {
            echo "Error: " . $e;
            exit();
        }
    }


    /**
     * Update the values of an entry from the category table
     * 
     * @param int       $pk_category_id category id of an entry, that will be updated
     * @param string    $category_text  updated category text
     */
    function updateCategory($pk_category_id, $category_text)
    {
        try {
            $stmt = $this->DB->prepare("UPDATE category SET category_text = :category_text WHERE pk_category_id = :pk_category_id");
            $stmt->bindParam(':pk_category_id', $pk_category_id, PDO::PARAM_INT);
            $stmt->bindParam(':category_text', $category_text, PDO::PARAM_STR);
            $stmt->execute();
            $this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException  $e) {
            echo "Error: " . $e;
            exit();
        }
    }

    /**
     * Update the values of an entry from the subcategory table
     * 
     * @param int       $pk_subcategory_id  subcategory id of an entry, that will be updated
     * @param string    $subcategory_text   updated subcategory text
     */
    function updateSubcategory($pk_subcategory_id, $subcategory_text)
    {
        try {
            $stmt = $this->DB->prepare("UPDATE subcategory SET subcategory_text = :subcategory_text WHERE pk_subcategory_id = :pk_subcategory_id");
            $stmt->bindParam(':pk_subcategory_id', $pk_subcategory_id, PDO::PARAM_INT);
            $stmt->bindParam(':subcategory_text', $subcategory_text, PDO::PARAM_STR);
            $stmt->execute();
            $this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException  $e) {
            echo "Error: " . $e;
            exit();
        }
    }

    /**
     * Update the values of an entry from the question table
     * 
     * @param int       $pk_question_id question id of an entry, that will be updated
     * @param string    $question_text  updated question text
     */
    function updateQuestion($pk_question_id, $question_text)
    {
        try {
            $stmt = $this->DB->prepare("UPDATE question SET question_text = :question_text WHERE pk_question_id = :pk_question_id");
            $stmt->bindParam(':pk_question_id', $pk_question_id, PDO::PARAM_INT);
            $stmt->bindParam(':question_text', $question_text, PDO::PARAM_STR);
            $stmt->execute();
            $this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException  $e) {
            echo "Error: " . $e;
            exit();
        }
    }

    /**
     * Update the values of an entry from the answer table
     * 
     * @param int       $pk_answer_id   answer id of an entry, that will be updated
     * @param string    $answer_text    updated answer text
     * @param boolean   $is_true        updated to true if the answer is correct
     */
    function updateAnswer($pk_answer_id, $answer_text, $is_true)
    {
        try {
            $stmt = $this->DB->prepare("UPDATE answer SET answer_text = :answer_text WHERE pk_answer_id = :pk_answer_id");
            $stmt->bindParam(':pk_answer_id', $pk_answer_id, PDO::PARAM_INT);
            $stmt->bindParam(':answer_text', $answer_text, PDO::PARAM_STR);
            $stmt->execute();
            $this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException  $e) {
            echo "Error: " . $e;
            exit();
        }

        try {
            $stmt = $this->DB->prepare("UPDATE answer SET is_true = :is_true WHERE pk_answer_id = :pk_answer_id");
            $stmt->bindParam(':pk_answer_id', $pk_answer_id, PDO::PARAM_INT);
            $stmt->bindParam(':is_true', $is_true, PDO::PARAM_BOOL);
            $stmt->execute();
            $this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException  $e) {
            echo "Error: " . $e;
            exit();
        }
    }


    /**
     * Create a select box from the entries of a given table name
     * 
     * @param string $name name of the table
     * @return string HTML select
     */
    function createSelectBox($name)
    {
        $result = '<select name="' . $name . '">';

        try {
            switch ($name) {
                case "category":
                    // if the table name is "category"
                    // -> get all entries from the category table and add them as option tags to the result variable
                    $stmt = $this->DB->prepare("SELECT pk_category_id, category_text FROM category");
                    if ($stmt->execute()) {
                        while ($row = $stmt->fetch()) {
                            $result .= $this->createSelectBoxOption($row['pk_category_id'], $row['category_text']);
                        }
                    }
                    break;
                case "subcategory":
                    // if the table name is "subcategory"
                    // -> get all entries from the subcategory table and add them as option tags to the result variable
                    $stmt = $this->DB->prepare("SELECT pk_subcategory_id, subcategory_text FROM subcategory");
                    if ($stmt->execute()) {
                        while ($row = $stmt->fetch()) {
                            $result .= $this->createSelectBoxOption($row['pk_subcategory_id'], $row['subcategory_text']);
                        }
                    }
                    break;
                case "question":
                    // if the table name is "question"
                    // -> get all entries from the question table and add them as option tags to the result variable
                    $stmt = $this->DB->prepare("SELECT pk_question_id, question_text FROM question");
                    if ($stmt->execute()) {
                        while ($row = $stmt->fetch()) {
                            $result .= $this->createSelectBoxOption($row['pk_question_id'], $row['question_text']);
                        }
                    }
                    break;
                case "answer":
                    // if the table name is "answer"
                    // -> get all entries from the answer table and add them as option tags to the result variable
                    $stmt = $this->DB->prepare("SELECT pk_answer_id, answer_text FROM answer");
                    if ($stmt->execute()) {
                        while ($row = $stmt->fetch()) {
                            $result .= $this->createSelectBoxOption($row['pk_answer_id'], $row['answer_text']);
                        }
                    }
                    break;
                default:
                    // if the switch statement doesn't match any table name -> close the select tag and return
                    return $result .= '</select>';
            }

            $this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException  $e) {
            echo "Error: " . $e;
            exit();
        }

        $result .= '</select>';
        return $result;
    }

    /**
     * Create an HTML option tag for the category / subcategory selection
     * 
     * @param int	 	$ID 		ID of the entry
     * @param string 	$text 		text value of the entry
     * @return string 	HTML option tag
     */
    function createSelectBoxOption($ID, $text)
    {
        return '<option value="' . $ID . '">' . $text . '</option>';
    }
}

?>


<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Quiz Admin - Krikler</title>
</head>

<body>
    <?php
    $admin = new Admin();
    ?>

    <?php include 'NAV.html'; ?>


    <div id="main">
        <div class="mainElement">
            <h1>Admin</h1>
        </div>

        <div class="mainElement">
            <h3>Werte hinzufügen</h3>
            <br>
            <table class="adminTable" id="insertTable">
                <tr>
                <form name="frm_insert_category" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="<?php echo $admin->getMethod() ?>">
                        <td>Kategorie</td>
                        <td><input type="text" name="category_text" required></input></td>
                        <td></td>
                        <td></td>
                        <td><input type="submit" name="insert" value="OK"></input></td>
                    </form>
                </tr>

                <tr>
                    <form name="frm_insert_subcategory" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="<?php echo $admin->getMethod() ?>">
                        <td>Unterkategorie</td>
                        <td><input type="text" name="subcategory_text" required></input></td>
                        <td><?php echo $admin->createSelectBox("category") ?></td>
                        <td></td>
                        <td><input type="submit" name="insert" value="OK"></input></td>
                    </form>
                </tr>

                <tr>
                    <form name="frm_insert_question" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="<?php echo $admin->getMethod() ?>">
                        <td>Frage</td>
                        <td><input type="text" name="question_text" required></input></td>
                        <td><?php echo $admin->createSelectBox("subcategory") ?></td>
                        <td></td>
                        <td><input type="submit" name="insert" value="OK"></input></td>
                    </form>
                </tr>

                <tr>
                    <form name="frm_insert_answer" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="<?php echo $admin->getMethod() ?>">
                        <td>Antwort</td>
                        <td><input type="text" name="answer_text" required></input></td>
                        <td><?php echo $admin->createSelectBox("question") ?></td>
                        <td><input type="checkbox" name="is_true" title="is_true"></input></td>
                        <td><input type="submit" name="insert" value="OK"></input></td>
                    </form>
                </tr>
            </table>
        </div>

        <div class="mainElement">
            <h3>Werte löschen</h3>
            <br>
            <table class="adminTable" id="deleteTable">
                <tr>
                    <form name="frm_delete_category" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="<?php echo $admin->getMethod() ?>">
                        <td>Kategorie</td>
                        <td><?php echo $admin->createSelectBox("category") ?></td>
                        <td><input type="submit" name="delete" value="OK"></input></td>
                    </form>
                </tr>

                <tr>
                    <form name="frm_delete_subcategory" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="<?php echo $admin->getMethod() ?>">
                        <td>Unterkategorie</td>
                        <td><?php echo $admin->createSelectBox("subcategory") ?></td>
                        <td><input type="submit" name="delete" value="OK"></input></td>
                    </form>
                </tr>

                <tr>
                    <form name="frm_delete_question" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="<?php echo $admin->getMethod() ?>">
                        <td>Frage</td>
                        <td><?php echo $admin->createSelectBox("question") ?></td>
                        <td><input type="submit" name="delete" value="OK"></input></td>
                    </form>
                </tr>

                <tr>
                    <form name="frm_delete_answer" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="<?php echo $admin->getMethod() ?>">
                        <td>Antwort</td>
                        <td><?php echo $admin->createSelectBox("answer") ?></td>
                        <td><input type="submit" name="delete" value="OK"></input></td>
                    </form>
                </tr>
            </table>
        </div>

        <div class="mainElement">
            <h3>Werte bearbeiten</h3>
            <br>
            <table class="adminTable" id="updateTable">
                <tr>
                    <form name="frm_update_category" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="<?php echo $admin->getMethod() ?>">
                        <td>Kategorie</td>
                        <td><input type="text" name="category_text" required></input></td>
                        <td><?php echo $admin->createSelectBox("category") ?></td>
                        <td></td>
                        <td><input type="submit" name="update" value="OK"></input></td>
                    </form>
                </tr>

                <tr>
                    <form name="frm_update_subcategory" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="<?php echo $admin->getMethod() ?>">
                        <td>Unterkategorie</td>
                        <td><input type="text" name="subcategory_text" required></input></td>
                        <td><?php echo $admin->createSelectBox("subcategory") ?></td>
                        <td></td>
                        <td><input type="submit" name="update" value="OK"></input></td>
                    </form>
                </tr>

                <tr>
                    <form name="frm_update_question" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="<?php echo $admin->getMethod() ?>">
                        <td>Frage</td>
                        <td><input type="text" name="question_text" required></input></td>
                        <td><?php echo $admin->createSelectBox("question") ?></td>
                        <td></td>
                        <td><input type="submit" name="update" value="OK"></input></td>
                    </form>
                </tr>

                <tr>
                    <form name="frm_update_answer" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="<?php echo $admin->getMethod() ?>">
                        <td>Antwort</td>
                        <td><input type="text" name="answer_text" required></input></td>
                        <td><?php echo $admin->createSelectBox("answer") ?></td>
                        <td><input type="checkbox" name="is_true" title="is_true"></input></td>
                        <td><input type="submit" name="update" value="OK"></input></td>
                    </form>
                </tr>
            </table>
        </div>
    </div>

</body>

<script>
    // get the update table element
    let updateTable = document.getElementById("updateTable");

    // go through four times, for each select box
    for (let i = 0; i < 4; i++) {
        let updateTextInput = updateTable.children[0].children[i].children[2].children[0];
        let updateSelectBox = updateTable.children[0].children[i].children[3].children[0];

        // when another entry of the select box has been selected
        // -> change the value of the corresponding text input
        updateSelectBox.addEventListener("change", function() {
            updateTextInput.value = updateSelectBox.selectedOptions[0].text;
        });
    }
</script>

</html>