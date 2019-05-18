<?php 
require_once('inc/functions.php');
$info = '';
$task = isset($_GET['task']) ? $_GET['task'] : 'report';
$error = isset($_GET['error']) ? $_GET['error'] : '0';

if ('delete' == $task) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
    if ($id>0) {
        deleteStudent($id);
        header('location: /crud/index.php?task=report');
    }
    
}

if ('seed' == $task) {
    seed();
    $info = 'Seed is complate';
}

if (isset($_POST['file_submit'])) {
    $preData = $_FILES['predata'];
    $preDataType = $_FILES['predata']['type'];
    $mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
    $preDataSize = $_FILES['predata']['size'];

    if (in_array($preDataType, $mimes)) {
        if ( $preDataSize < 1048576) {
            processImportData($preData);
        } else {
            echo $warning = "We are not accepting more than 1MB file!";
        }   
    } else {
        echo $warning = "We are not accepting without CSV file!";
    }
}

$fname  = '';
$lname = '';
$roll  = '';
if (isset($_POST['submit'])) {
    $fname = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING);
    $lname = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_STRING);
    $roll  = filter_input(INPUT_POST, 'roll', FILTER_SANITIZE_STRING);
    $id    = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);

    if ($id) {
        // Update the exiesting student
        if ($id !='' && $fname !='' && $lname !='' && $roll !='') {
            $result = updateStudent($id, $fname, $lname, $roll);

            if ($result) {
                header('location: /crud/index.php?task=report');
            } else{
                $error = "1";
            }
        }
        
    } else{
        // Add a new student
        if ($fname !='' && $lname !='' && $roll !='') {
            $result = addStudent($fname, $lname, $roll);
    
            if ($result) {
                header('location: /crud/index.php?task=report');
            } else{
                $error = "1";
            }
        }
    }

    

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CRUD Project</title>
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
    <link rel="stylesheet" href="//cdn.rawgit.com/necolas/normalize.css/master/normalize.css">
    <link rel="stylesheet" href="//cdn.rawgit.com/milligram/milligram/master/dist/milligram.min.css">
    
</head>
<body>
<div class="container">
    <div class="row">
        <div class="column column-80 column-offset-10">
            <h2>Project-2 CRUD</h2>
            <p>A simple project to perform CRUD operations using plan file and php</p>
            <?php include_once('inc/templates/nav.php'); ?>
            <br>
            <?php 
                if($info != ''){
                    echo $info;
                } 
            ?>

        </div>
    </div>
    
    <!--Section for import handling-->
    <?php if ('import' == $task): ?>
        <div class="row">
            <div class="column column-80 column-offset-10">
                <blockquote>
                    <p>The seed, you can use for add a few demo data for checking look and feel of real data.</p>
                </blockquote>
                <a class="button" href="/crud/index.php?task=seed">Seed</a>

                <blockquote>
                    <p>Uploade csv file here for add students data. The csv file format must will be like:</p>
                    <p>First Name, Last Name, Roll, Email</p>
                </blockquote>

                <form action="/crud/index.php?task=import" method="post" enctype="multipart/form-data">
                    <fieldset>
                        <label for="predata">Import from csv</label>
                        <input type="file" name="predata" id="predata"><br>
                        <input class="button-primary" type="submit" value="Submit" name="file_submit">
                    </fieldset>
                </form>
            </div>
        </div>
    <?php endif; ?>
    <!--End section import handling-->

    <?php if ('1' == $error): ?>
        <div class="row">
            <div class="column column-80 column-offset-10">
                <blockquote>
                    Duplicate roll numbar!
                </blockquote>
            </div>
        </div>
    <?php endif; ?>
    <?php if ('report' == $task): ?>
        <div class="row">
            <div class="column column-80 column-offset-10">
                <?php generateReport(); ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if('add' == $task): ?>
        <div class="row">
            <div class="column column-80 column-offset-10">

                <form action="/crud/index.php?task=add" method="post">
                    <fieldset>
                        <label for="fname">First Name</label>
                        <input type="text" name="fname" id="fname" value="<?php echo $fname; ?>">
                        <label for="lname">Last Name</label>
                        <input type="text" name="lname" id="lname"  value="<?php echo $lname; ?>">
                        <label for="roll">Roll</label>
                        <input type="number" name="roll" id="roll"  value="<?php echo $roll; ?>">
                        
                        <input class="button-primary" type="submit" value="Save" name="submit">
                    </fieldset>
                </form>
                            
            </div>
        </div>
    <?php endif; ?>
    <?php 
    if('edit' == $task): 
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
        $student = getStudents($id);
        if($student):
        
    ?>
        <div class="row">
            <div class="column column-80 column-offset-10">

                <form method="post">
                    <fieldset>
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <label for="fname">First Name</label>
                        <input type="text" name="fname" id="fname" value="<?php echo $student['fname']; ?>">
                        <label for="lname">Last Name</label>
                        <input type="text" name="lname" id="lname"  value="<?php echo $student['lname']; ?>">
                        <label for="roll">Roll</label>
                        <input type="number" name="roll" id="roll"  value="<?php echo $student['roll']; ?>">
                        
                        <input class="button-primary" type="submit" value="Update" name="submit">
                    </fieldset>
                </form>
                            
            </div>
        </div>
<?php 
    endif;
endif; 
?>
</div>

    <script src="assets/js/script.js"></script>
</body>
</html>