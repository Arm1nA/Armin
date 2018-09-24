
<?php
  $user = 'a00828482';
  $pass = 'saliii6913';
  $database = 'lab';
 
  // establish database connection
  $conn = oci_connect($user, $pass, $database);
  if (!$conn) exit;
?>

<html>
<head>
</head>
<body>
  <div>
    <form id='searchform' action='index.php' method='get'>
      <a href='index.php'>Alle Kunden</a> ---
      Suche nach Nachname: 
      <input id='search' name='search' type='text' size='20' value='<?php echo $_GET['search']; ?>' />
      <input id='submit' type='submit' value='Los!' />
    </form>
  </div>
<?php
  // check if search view of list view
  if (isset($_GET['search'])) {
    $sql = "SELECT * FROM Kunde WHERE NName like '%" . $_GET['search'] . "%'";
  } else {
    $sql = "SELECT * FROM Kunde";
  }

  // execute sql statement
  $stmt = oci_parse($conn, $sql);
  oci_execute($stmt);
?>
  <table style='border: 1px solid #DDDDDD'>
    <thead>
      <tr>
        <th>KN</th>
        <th>VName</th>
        <th>NName</th>
        <th>Geb</th>
      </tr>
    </thead>
    <tbody>
<?php
  // fetch rows of the executed sql query
  while ($row = oci_fetch_assoc($stmt)) {

    echo "<tr>";
    echo "<td>" . $row['KN'] . "</td>";
    echo "<td>" . $row['VNAME'] . " " . $row['NNAME'] . "</td>";
    echo "<td>geb. am " . $row['GEB'] . "</td>";
    echo "</tr>";
  }
?>
    </tbody>
  </table>
<div>Insgesamt <?php echo oci_num_rows($stmt); ?> Kunden gefunden!</div>
<?php  oci_free_statement($stmt); ?>

<div>
  <form id='insertform' action='index.php' method='get'>
    Neue Abteilung einfuegen:
	<table style='border: 1px solid #DDDDDD'>
	  <thead>
	    <tr>
	      <th>ID</th>
	      <th>Name</th>
	      <th>Flaeche</th>
	    </tr>
	  </thead>
	  <tbody>
	     <tr>
	        <td>
	           <input id='ID' name='ID' type='text' size='10' value='<?php echo $_GET['ID']; ?>' />
                </td>
                <td>
                   <input id='Name' name='Name' type='text' size='20' value='<?php echo $_GET['Name']; ?>' />
                </td>
		<td>
		   <input id='Flaeche' name='Flaeche' type='text' size='20' value='<?php echo $_GET['Flaeche']; ?>' />
		</td>
	      </tr>
           </tbody>
        </table>
        <input id='submit' type='submit' value='Insert!' />
  </form>
</div>


<?php
  //Handle insert
  if (isset($_GET['ID'])) 
  {
    //Prepare insert statementd
    $sql = "INSERT INTO Abteilung VALUES(" . $_GET['ID'] . ",'"  . $_GET['Name'] . "','" . $_GET['Flaeche'] . "')";
    //Parse and execute statement
    $insert = oci_parse($conn, $sql);
    oci_execute($insert);
    $conn_err=oci_error($conn);
    $insert_err=oci_error($insert);
    if(!$conn_err & !$insert_err){
	print("Successfully inserted");
 	print("<br>");
    }
    //Print potential errors and warnings
    else{
       print($conn_err);
       print_r($insert_err);
       print("<br>");
    }
    oci_free_statement($insert);
  } 
?>

<div>
  <form id='searchabt' action='index.php' method='get'>
    Suche Artikel zu bestimmter Abteilung (Name):
      <input id='Name' name='Name' type='text' size='20' value='<?php echo $_GET['Name']; ?>' />
      <input id='submit' type='submit' value='Aufruf Stored Procedure!' />
  </form>
</div>

<?php
  //Handle Stored Procedure
  if (isset($_GET['Name']))
  {
	  //Call Stored Procedure	
	  $Name = $_GET['Name'];
	  $abtnr='';
	  $sproc = oci_parse($conn, 'begin art_abt(:p1, :p2); end;');
	  //Bind variables, p1=input (Name), p2=output (abtnr)
	  oci_bind_by_name($sproc, ':p1', $Name);
	  oci_bind_by_name($sproc, ':p2', $abtnr,20);
	  oci_execute($sproc);
	  $conn_err=oci_error($conn);
	  $proc_err=oci_error($sproc);

	  if(!$conn_err && !$proc_err){
	     echo("<br><b>" . $Name . " (Artikel) befindet sich in Abteilung Nr. " . $abtnr . "</b><br>" );  // prints OUT parameter of stored procedure
	  }
	  else{
	     //Print potential errors and warnings
	     print($conn_err);
	     print_r($proc_err);
	  }  
  }

  
  // clean up connections
  oci_free_statement($sproc);
  oci_close($conn);
?>
</body>
</html>
