<?php
// Start the session
session_start();

/**
 * PHP-Quiz class
 * 
 * DB connection
 * category (& subcategory) selection form
 * quiz form: questions & answers (selectable through checkboxes)
 * quiz evaluation: count the correct answers; styling for correct & incorrect answers
 */
class Quiz
{
	private $DB;
	private $method = "get";

	/**
	 * Constructor for a new questionnaire
	 */
	function __construct()
	{
		$this->DB = include 'DB.php';
	}

	/**
	 * Create selection forms for the category & subcategory
	 * 
	 * @return string HTML category / subcategory forms inside a table
	 */
	function createCategorySubcategoryForms()
	{
		// if category / subcategory is set -> override the corresponding session variable with that value
		if (isset($_GET['category'])) {
			$_SESSION['category'] = $_GET['category'];
		} else if (isset($_GET['subcategory'])) {
			$_SESSION['subcategory'] = $_GET['subcategory'];
		}

		$result = '<table>';
		$result .= '<tr><th>Kategorie</th><th>Unterkategorie</th></tr>';

		$result .= '<tr><td>';
		$result .= '<form name="frm_category" action="' . $_SERVER['PHP_SELF'] . '" method="' . $this->method . '">';
		$result .= $this->createCategorySelectBox();
		$result .= '</form></td>';

		$result .= '<td>';
		$result .= '<form name="frm_subcategory" action="' . $_SERVER['PHP_SELF'] . '" method="' . $this->method . '">';
		$result .= $this->createSubcategorySelectBox();
		$result .= '</form></td></tr>';
		$result .= '</table>';

		return $result;
	}

	/**
	 * Create a category select box
	 * 
	 * containing the current values from the DB
	 * @return string HTML category select box
	 */
	function createCategorySelectBox()
	{
		// when changing the value of the select box -> submit the form
		$result = '<select name="category" onchange="submit();">';
		// if category has not yet been selected -> display an empty placeholder,
		// which is not displayed in the dropdown or is selectable
		$result .= '<option value="none" selected disabled hidden></option>';

		try {
			// get the categories from the DB
			$stmt = $this->DB->prepare("SELECT pk_category_id, category_text FROM category");

			if ($stmt->execute()) {
				while ($row = $stmt->fetch()) {
					$selected = false;
					// only if category or subcategory or check_answers (session var) is set
					// -> check if the current category_id is equals to the category_id in the session variable
					// if it is the same -> select the option element, in order to be displayed (signaling the currently selected category)
					if (isset($_GET['category']) || isset($_GET['subcategory']) || isset($_GET['check_answers'])) {
						if ($row['pk_category_id'] == $_SESSION['category']) {
							$selected = true;
						}
					}
					// add the HTML option element with the category_id as the value and the category_text as the display text
					$result .= $this->createSelectBoxOption($row['pk_category_id'], $row['category_text'], $selected);
				}
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
	 * Create a subcategory select box
	 * 
	 * containing the current values from the DB
	 * @return string HTML subcategory select box
	 */
	function createSubcategorySelectBox()
	{
		// when changing the value of the select box -> submit the form
		$result = '<select name="subcategory" onchange="submit();">';
		// if subcategory has not yet been selected -> display an empty placeholder,
		// which is not displayed in the dropdown or is selectable
		$result .= '<option value="none" selected disabled hidden></option>';

		try {
			// only if category or subcategory or check_answers (session var) is set
			// -> create the SQL statement, for retrieving all subcategories, which belong to the category_id from the session var
			if (isset($_GET['category']) || isset($_GET['subcategory']) || isset($_GET['check_answers'])) {
				// get the categories from the DB
				$stmt = $this->DB->prepare("SELECT pk_subcategory_id, subcategory_text FROM subcategory WHERE fk_pk_category_id = :pk_category_id");
				$stmt->bindParam(':pk_category_id', $_SESSION['category'], PDO::PARAM_INT);
			} else {
				// if category or subcategory or check_answers is not set -> close the select HTML tag and return the temporary variable
				return $result . '</select>';
			}

			if ($stmt->execute()) {
				while ($row = $stmt->fetch()) {
					$selected = false;
					// only if subcategory or check_answers (session var) is set
					// -> check if the current subcategory_id is equals to the subcategory_id in the session variable
					// if it is the same -> select the option element, in order to be displayed (signaling the currently selected subcategory)
					if (isset($_GET['subcategory']) || isset($_GET['check_answers'])) {
						if ($row['pk_subcategory_id'] == $_SESSION['subcategory']) {
							$selected = true;
						}
					}
					// add the HTML option element with the subcategory_id as the value and the subcategory_text as the display text
					$result .= $this->createSelectBoxOption($row['pk_subcategory_id'], $row['subcategory_text'], $selected);
				}
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
	 * @param int	 	$ID 		category or subcategory ID
	 * @param string 	$text 		category or subcategory text
	 * @param boolean 	$selected 	true if the current value should be selected
	 * @return string 	HTML option tag
	 */
	function createSelectBoxOption($ID, $text, $selected)
	{
		if ($selected) {
			return '<option value="' . $ID . '" selected>' . $text . '</option>';
		} else {
			return '<option value="' . $ID . '">' . $text . '</option>';
		}
	}


	/**
	 * The amount of correctly answered questions
	 * 
	 * @var int $correct_answered_questions
	 */
	private $correct_answered_questions = 0;

	/**
	 * Create a quiz form
	 * 
	 * containing the questions that belong to the selected subcategory
	 * containing the answers that belong to the questions
	 * 
	 * @return string HTML form
	 */
	function createQuiz()
	{
		// if subcategory and check_answers are not set
		// -> return nothing (no quiz is visible, because no subcategory has been selected or the answers are currently not being checked)
		if (!isset($_GET['subcategory']) && !isset($_GET['check_answers'])) {
			return "";
		}

		$result = '<form name="frm_quiz" action="' . $_SERVER['PHP_SELF'] . '" method="' . $this->method . '">';

		try {
			// get all the questions from the DB that belong the subcategory (session var)
			$questionStmt = $this->DB->prepare("SELECT pk_question_id, question_text FROM question WHERE fk_pk_subcategory_id = :pk_subcategory_id");
			$questionStmt->bindParam(':pk_subcategory_id', $_SESSION['subcategory'], PDO::PARAM_INT);

			if ($questionStmt->execute()) {
				while ($row = $questionStmt->fetch()) {
					// for each question create a div element,
					// that contains the question text and the answers in an unordered list
					$result .= '<div>';
					$result .= '<p>' . $row['question_text'] . '</p>';
					$result .= $this->createAnswerCheckboxes($row['pk_question_id']);
					$result .= '</div>';
				}
			}

			$this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException  $e) {
			echo "Error: " . $e;
			exit();
		}

		$disabled = "";
		// if the answers are currently being checked -> disable the button from being clickable
		if (isset($_GET['check_answers'])) {
			$disabled = "disabled";
		}
		$result .= '<button onchange="submit();" name="check_answers" ' . $disabled . '>Überprüfen!</button>';
		$result .= '</form>';

		return $result;
	}

	/**
	 * Create answer checkboxes
	 * 
	 * inside of an unordered list
	 * 
	 * @param int $question_id question id of the answers
	 * @return string HTML ul
	 */
	function createAnswerCheckboxes($question_id)
	{
		$result = "";
		// the amount of checked checkboxes, which are required for a correctly answered question
		$expected_correct_answers = 0;
		// the actual amount of correctly checked checkboxes
		$correct_answers = 0;

		try {
			// get all the possible answers from the DB that belong to the question_id
			$questionStmt = $this->DB->prepare("SELECT pk_answer_id, answer_text FROM answer WHERE fk_pk_question_id = :pk_question_id");
			$questionStmt->bindParam(':pk_question_id', $question_id, PDO::PARAM_INT);

			$result .= '<ul>';
			if ($questionStmt->execute()) {
				while ($row = $questionStmt->fetch()) {
					// add a list item for each possible answer
					$result .= '<li>';

					// if check_answers is set -> check if the the selected answers are correct
					// otherwise -> just add the checkbox and the label, which contains the answer
					if (isset($_GET['check_answers'])) {
						// check if the current answer_id of the loop is a correct answer -> increment expected_correct_answers 
						if ($this->isCorrect($row['pk_answer_id'])) {
							$expected_correct_answers++;
						}

						// check if the current answer_id has been selected and is correct -> increment correct_answers
						if (isset($_GET['answer' . $row['pk_answer_id']]) && $this->isCorrect($row['pk_answer_id'])) {
							$correct_answers++;
						}

						// if the current answer_id has been selected
						// -> set the checkbox state to checked (in order to display that it has been checked)
						$checked = "";
						if (isset($_GET['answer' . $row['pk_answer_id']])) {
							$checked = "checked";
						}
						$result .= '<input type="checkbox" name="answer' . $row['pk_answer_id'] . '" value="1" disabled ' . $checked . '>';

						// add a "correct" or "incorrect" class to the label element for appropriate CSS styling
						$class = "";
						if ($this->isCorrect($row['pk_answer_id'])) {
							// if the current answer_id is correct -> display green
							$class = "correct";
						} else if (isset($_GET['answer' . $row['pk_answer_id']])) {
							// if the current answer_id is not correct but it still has been checked
							// -> display red and decrement the correct_answers var
							$class = "incorrect";
							$correct_answers--;
						}
						$result .= '<label class="' . $class . '" for="answer' . $row['pk_answer_id'] . '">' . $row['answer_text'] . '</label><br>';
					} else {
						$result .= '<input type="checkbox" name="answer' . $row['pk_answer_id'] . '" value="1">';
						$result .= '<label for="answer' . $row['pk_answer_id'] . '">' . $row['answer_text'] . '</label><br>';
					}
					$result .= '</li>';
				}
			}
			$result .= '</ul>';

			$this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException  $e) {
			echo "Error: " . $e;
			exit();
		}

		// if the expected amount of checked answers is the same as the actual amount
		// -> increment the correct_answered_questions var
		if ($expected_correct_answers == $correct_answers) {
			$this->correct_answered_questions++;
		}

		return $result;
	}

	/**
	 * Check if the answer at the given id is correct
	 * 
	 * @param int $answer_id answer id of the answer
	 * @return boolean true if the answer is correct
	 */
	function isCorrect($answer_id)
	{
		try {
			// get the is_true attribute of the given answer_id
			$questionStmt = $this->DB->prepare("SELECT is_true FROM answer WHERE pk_answer_id = :pk_answer_id");
			$questionStmt->bindParam(':pk_answer_id', $answer_id, PDO::PARAM_INT);

			if ($questionStmt->execute()) {
				$row = $questionStmt->fetch();
				// if is_true equals to 1 -> return true
				return $row['is_true'] == 1;
			}

			$this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException  $e) {
			echo "Error: " . $e;
			exit();
		}
	}

	/**
	 * Get the amount of questions the belong to the subcategory (session var)
	 * 
	 * @return int amount of questions
	 */
	function getAmountOfQuestions()
	{
		try {
			// count the questions, which have have the question_id of the session var
			$questionStmt = $this->DB->prepare("SELECT COUNT(pk_question_id) AS question_count
				FROM question
					 INNER JOIN subcategory ON fk_pk_subcategory_id = pk_subcategory_id
				WHERE fk_pk_subcategory_id = :pk_subcategory_id");
			$questionStmt->bindParam(':pk_subcategory_id', $_SESSION['subcategory'], PDO::PARAM_INT);

			if ($questionStmt->execute()) {
				$row = $questionStmt->fetch();
				return $row['question_count'];
			}

			$this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException  $e) {
			echo "Error: " . $e;
			exit();
		}
	}

	/**
	 * Generate the result of the quiz
	 * 
	 * @return string HTML paragraph
	 */
	function generateResult()
	{
		// call the getAmountOfQuestions function and store the value
		$questions_count = $this->getAmountOfQuestions();
		// calculate the percentage of correctly answered questions
		$percentage = ($this->correct_answered_questions / $questions_count) * 100;

		// display the amount of correctly answered questions of the amount of questions
		$result = '<p>' . $this->correct_answered_questions . ' von ' . $questions_count;
		// display the correctly answered questions percentage
		$result .= ' Fragen richtig beantwortet (' . $percentage . '%)!</p>';
		return $result;
	}
}

?>


<!DOCTYPE html>
<html lang="de">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="style.css">
	<title>Quiz - Krikler</title>
</head>

<body>
	<?php
	// initialize a new Quiz object
	$quiz = new Quiz();
	?>

	<?php include 'NAV.html'; ?>

	<div id="main">
		<div class="mainElement">
			<h1>Quiz</h1>
		</div>

		<div class="mainElement" id="categorySubcategorySelection">
			<?php
			echo $quiz->createCategorySubcategoryForms();
			?>
		</div>

		<div class="mainElement" id="quiz">
			<?php
			echo $quiz->createQuiz();
			?>
		</div>

		<!-- Display the result, only if the answers should be checked -->
		<?php if (isset($_GET['check_answers'])) {
			echo '<div class="mainElement " id="result">';
			echo $quiz->generateResult();
			echo '</div>';
		} ?>
	</div>

</body>

</html>