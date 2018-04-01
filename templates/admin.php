<?php
include_once ("ressources/handle_db.php");
if (!isset($_SESSION)) {
    session_start();
}

if (isset($_POST['submit']) && $_POST['submit'] === "Ajouter un produit") {
    if (!isset($_POST['name']) || !isset($_POST['description']) || !isset($_POST['img']) || !isset($_POST['price']) || !isset($_POST['stock']) || 
    $_POST['name'] === "" || $_POST['description'] === "" || $_POST['img'] === "" || $_POST['price'] === "" || $_POST['stock'] === "") {
        echo "Tous les champs doivent être remplis.\n";
    }
    else {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $img = $_POST['img'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        query("INSERT INTO product (name, description, img, price, stock) VALUES ('$name', '$description', '$img', '$price', '$stock')");
    }
}

if (isset($_POST['submit']) && $_POST['submit'] == "Modifier le produit") {
    if (!isset($_POST['name']) || !isset($_POST['description']) || !isset($_POST['img']) || !isset($_POST['price']) || !isset($_POST['stock']) || 
    $_POST['name'] === "" || $_POST['description'] === "" || $_POST['img'] === "" || $_POST['price'] === "" || $_POST['stock'] === "") {
        echo "Tous les champs doivent être remplis.\n";
    }
    else {
        $name = mysql_real_escape_string($_POST['name']);
        $description = mysql_real_escape_string($_POST['description']);
        $img = mysql_real_escape_string($_POST['img']);
        $price = mysql_real_escape_string($_POST['price']);
        $stock = mysql_real_escape_string($_POST['stock']);
        query("UPDATE `product` SET name = '$name', description = '$description', img = '$img', price = '$price', stock = '$stock' WHERE name = '$_POST[product]'");
    }
}

if (isset($_POST['submit']) && $_POST['submit'] == "Supprimer le produit") {
        query("DELETE FROM product WHERE name = '$_POST[name]'");
}

if (isset($_POST['submit']) && $_POST['submit'] === "Ajouter une catégorie") {
    if (!isset($_POST['name']) || $_POST['name'] === "") {
        echo "Le champ doit être rempli.\n";
    }
    else {
        $name = $_POST['name'];
        query("INSERT INTO category (name) VALUES ('$name')");
    }
}

if (isset($_POST['submit']) && $_POST['submit'] === "Modifier la catégorie") {
    if (!isset($_POST['name']) || $_POST['name'] === "" || !isset($_POST['newname']) || $_POST['newname'] === "") {
        echo "Le champ doit être rempli.\n";
    }
    else {
        query("UPDATE `category` SET name = '$_POST[newname]' WHERE name = '$_POST[name]'");
    }
}

if (isset($_POST['submit']) && $_POST['submit'] === "Lier une categorie") {
    query("REPLACE INTO category_map (category_id, product_id) VALUE ((SELECT id from category where name = '$_POST[category]'), (select id from product where name = '$_POST[product]')) ");
}

if (isset($_POST['submit']) && $_POST['submit'] == "Supprimer la categorie") {
    query("DELETE FROM category WHERE name = '$_POST[name]'");
}

function check_error_form()
{
  $error = TRUE;
  if (preg_match("/^.+@.+\..+$/", $_POST['email']) == FALSE && $_POST['email'] !== 'admin') {
    $error = "L'adresse email n'est pas valide.";
    return ($error);
  }
  else if (strlen($_POST['passwd']) < 5) {
    $error = "Le mot de passe doit faire au moins 5 caractères.";
    return ($error);
  }
  else if (preg_match('~[0-9]+~', $_POST['passwd']) == FALSE) {
    $error = "Le mot de passe doit comporter au moins un chiffre.";
    return ($error);
  }
  else if (preg_match("/^[0-9]+\s+.+\s+.+\s?$/", $_POST['address'] && $_POST['address'] !== 'admin') == FALSE) {
    $error = "L'adresse n'est pas valide.";
    return ($error);
  }
  else if (preg_match("/^[0-9]{5}$/", $_POST['postal_code']) == FALSE) {
    $error = "Le code postal n'est pas valide.";
    return ($error);
  }
  else if (preg_match("/^\+?[0-9]+$/", $_POST['phone']) == FALSE) {
    $error = "Le numéro de téléphone n'est pas valide.";
    return ($error);
  }
  if (!isset($_POST['fname']) || !isset($_POST['lname']) || !isset($_POST['email'])
  || !isset($_POST['passwd']) || $_POST['fname'] === "" || $_POST['lname'] === "" || $_POST['email'] === "" || $_POST['passwd'] === ""){
    $error = "Tous les champs obligatoires doivent être remplis.";
    return ($error);
  }
  $ret = query("SELECT * FROM `user`");
  while ($row = mysqli_fetch_assoc($ret))
  {
    if ($row['email'] === $_POST['email']) {
      $error = "L'adresse email fournie est déjà utilisée par un autre compte.";
      return ($error);
    }
  }
  return ($error);
}

if (isset($_POST['submit']) && $_POST['submit'] === "Ajouter un compte")
{
  $error = check_error_form();
  {
    if (isset($error) && $error === TRUE) {
      $query = "INSERT INTO `user` (passwd, fname, lname, email, address, city, postal_code, phone) VALUES ('" . hash('whirlpool', $_POST['passwd']) . "', '{$_POST['fname']}', '{$_POST['lname']}', '{$_POST['email']}', '{$_POST['address']}', '{$_POST['city']}', '{$_POST['postal_code']}', '{$_POST['phone']}')";
      query($query);
    }
    else if (isset($error)) {
      echo '<script> alert("'.$error.'");</script>';
    }
  }
}

function check_error_form_change($check_pw)
{
  $error = TRUE;
  if (!isset($_POST['fname']) || !isset($_POST['lname']) || !isset($_POST['email'])
  || !isset($_POST['oldpw']) || $_POST['fname'] === "" || $_POST['lname'] === "" || $_POST['email'] === "" || $_POST['oldpw'] === ""){
    $error = "Tous les champs obligatoires doivent être remplis.";
    return ($error);
  }
  if (preg_match("/^.+@.+\..+$/", $_POST['email']) == FALSE) {
    $error = "L'adresse email n'est pas valide.";
    return ($error);
  }
  else if (preg_match("/^[0-9]+\s+.+\s+.+\s?$/", $_POST['address']) == FALSE) {
    $error = "L'adresse n'est pas valide.";
    return ($error);
  }
  else if (preg_match("/^[0-9]{5}$/", $_POST['postal_code']) == FALSE) {
    $error = "Le code postal n'est pas valide.";
    return ($error);
  }
  else if (preg_match("/^\+?[0-9]+$/", $_POST['phone']) == FALSE) {
    $error = "Le numéro de téléphone n'est pas valide.";
    return ($error);
  }
  else if (isset($_POST['oldpw']) && hash("whirlpool", $_POST['oldpw']) !== $check_pw['passwd']) {
    $error = "Ancien mot de passe erroné.\n";
    return ($error);
  }
  else if (isset($_POST['oldpw']) ) {
    if (strlen($_POST['newpw']) < 5) {
      $error = "Le nouveau mot de passe doit faire au moins 5 caractères.";
      return ($error);
    }
    else if (preg_match('~[0-9]+~', $_POST['newpw']) == FALSE) {
      $error = "Le nouveau mot de passe doit comporter au moins un chiffre.";
      return ($error);
    }
  }
  return ($error);
}
if (isset($_POST['submit']) && $_POST['submit'] == "Modifier le compte")
{
  $query = query("SELECT passwd FROM `user` WHERE email = '$_POST[email]'");
  if (mysqli_num_rows($query) > 0)
    $check_pw = mysqli_fetch_assoc($query);
  $error = check_error_form_change($check_pw);
  if ($error === TRUE)
  {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    if (isset($oldpw))
      $oldpw = $_POST['oldpw'];
    if (isset($newpw))
      $newpw = $_POST['newpw'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $postal_code = $_POST['postal_code'];
    $phone = $_POST['phone'];
    $change = "UPDATE `user` SET passwd ='" . hash('whirlpool', $_POST['newpw']) . "', fname = '$fname', lname = '$lname', email = '$email', address = '$address', city = '$city', postal_code = '$postal_code', phone = '$phone' WHERE email = '$_SESSION[user_email]'";
    query($change);
    $_SESSION['user_email'] = $_POST['email'];
  }
  else
    echo $error."\n";
}

if (isset($_POST['submit']) && $_POST['submit'] == "Supprimer le compte") {
    query("DELETE FROM user WHERE lname = '$_POST[lname]'");
}

?>

<h2>Gérer les produits</h2>
<form method="post" id="add_object">
  <fieldset>
    <h3>Ajouter un produit</h3>
    <label for="name">Nom : </label><input id="name" name="name" type="text"/><br/>
    <label for="description">Description : </label><input id="description" name="description" type="text"/><br/>
    <label for="img">Image : </label><input id="img" name="img" type="url"/><br/>
    <label for="price">Prix : </label><input id="price" type="number" name="price"/><br/>
    <label for="stock">Stock : </label><input id="stock" name ="stock" type="number"/><br/>
    <input type="submit" class="submit" name="submit" value="Ajouter un produit" ><br/>
  </fieldset>
</form>

<form method="post" id="change_object">
  <fieldset>
  <h3>Modifiez votre produit</h3>      
  <?php
    $ret = query("SELECT name FROM `product`");

    echo <<<EOL
        <form method="post">
        <select name="product">
            <option value="all">Aucune</option>
EOL;
    if (mysqli_num_rows($ret) > 0) {
        while($row = mysqli_fetch_assoc($ret)) {
           if (isset($_POST['product']) && $row["name"] == $_POST['product'])
           {
            echo <<<EOL
            <option value="$row[name]" selected >$row[name]</option>
EOL;
           }
           else
            echo <<<EOL
            <option value="$row[name]" >$row[name]</option>
EOL;
        }
    }
    echo <<<EOL
    <input type='submit' name='submit' value="valider"/>
    </select>
    </form>
EOL;
?>
<?php
        if (isset($_POST["product"])){
        $ret = query("SELECT * FROM `product` WHERE name = '$_POST[product]'");
          if (mysqli_num_rows($ret) > 0) {
            while($row = mysqli_fetch_assoc($ret)) {
              echo <<<EOL
             </br>
            <label for="name">Nom : </label><input id="name" name="name" type="text" value="$row[name]"/><br/>
            <label for="description">Description : </label><input id="description" name="description" type="text" value="$row[description]"/><br/>
            <label for="img">Image : </label><input id="img" name="img" type="url" value="$row[img]"/><br/>
            <label for="price">Prix : </label><input id="price" type="number" name="price" value="$row[price]"/><br/>
            <label for="stock">Stock : </label><input id="stock" name ="stock" type="number" value="$row[stock]"/><br/>
            <input type="submit" class="submit" name="submit" value="Modifier le produit"><br/>
EOL;
           }
        }  
      }
?>
  </fieldset>
</form>

<form method="post" id="delete_object">
  <fieldset>
  <h3>Supprimer un produit</h3>      
  <?php      
    $ret = query("SELECT name FROM `product`");
    echo <<<EOL
        <form method="post">
        <select name="name">
            <option value="all">Aucune</option>
EOL;
    if (mysqli_num_rows($ret) > 0) {
        while($row = mysqli_fetch_assoc($ret)) {
            echo <<<EOL
            <option value="$row[name]">$row[name]</option>
EOL;
        }
    }
    echo <<<EOL
    <input type='submit' name='submit' value="Supprimer le produit"/>
    </select>
    </form>
EOL;
?>
  </fieldset>
</form>

<h2>Gérer les catégories</h2>
<form method="post" id="add_categorie">
  <fieldset>
    <h3>Ajouter une catégorie</h3>
    <label for="name">Nom : </label><input id="name" name="name" type="text"/><br/>
    <input type="submit" class="submit" name="submit" value="Ajouter une catégorie" ><br/>
  </fieldset>
</form>


<form method="post" id="link_category">
  <fieldset>
  <h3>Modifiez une catégorie</h3>      
  <?php      
    $ret = query("SELECT name FROM `category`");
    echo <<<EOL
        <form method="post">
        <select name="category">
            <option value="all">Aucune</option>
EOL;
    if (mysqli_num_rows($ret) > 0) {
        while($row = mysqli_fetch_assoc($ret)) {
            echo <<<EOL
            <option value="$row[name]">$row[name]</option>
EOL;
        }
    }
    $ret = query("SELECT name FROM `product`");
    echo <<<EOL
        </select>
        <select name="product">
            <option value="all">Aucune</option>
EOL;
    if (mysqli_num_rows($ret) > 0) {
        while($row = mysqli_fetch_assoc($ret)) {
            echo <<<EOL
            <option value="$row[name]">$row[name]</option>
EOL;
        }
    }
    echo <<<EOL
    <input type='submit' name='submit' value="Lier une categorie"/>
    </select>
    </form>
EOL;
?>
  </fieldset>
</form>
<form method="post" id="change_category">
  <fieldset>
  <h3>Modifiez une catégorie</h3>
    
  <?php
    $ret = query("SELECT name FROM `category`");

    echo <<<EOL
        <form method="post">
        <select name="category">
            <option value="all">Aucune</option>
EOL;
    if (mysqli_num_rows($ret) > 0) {
        while($row = mysqli_fetch_assoc($ret)) {
           if (isset($_POST['category']) && $row["name"] == $_POST['category'])
           {
            echo <<<EOL
            <option value="$row[name]" selected >$row[name]</option>
EOL;
           }
           else
            echo <<<EOL
            <option value="$row[name]" >$row[name]</option>
EOL;
        }
    }
    echo <<<EOL
    <input type='submit' name='submit' value="valider"/>
    </select>
    </form>
EOL;
?>
<?php
        if (isset($_POST["category"])){
        $ret = query("SELECT * FROM `category` WHERE name = '$_POST[category]'");
          if (mysqli_num_rows($ret) > 0) {
            while($row = mysqli_fetch_assoc($ret)) {
              echo <<<EOL
              </br>
            <label for="name">Ancien nom : </label><input id="name" name="name" type="text" value="$row[name]"/><br/>
            <label for="name">Nouveau nom : </label><input id="newname" name="newname" type="text""/><br/>
            <input type="submit" class="submit" name="submit" value="Modifier la catégorie"><br/>
EOL;
           }
        }  
    }
?>
  </fieldset>
</form>

<form method="post" id="delete_category">
  <fieldset>
  <h3>Supprimer une catégorie</h3>      
  <?php      
    $ret = query("SELECT name FROM `category`");
    echo <<<EOL
        <form method="post">
        <select name="name">
            <option value="all">Aucune</option>
EOL;
    if (mysqli_num_rows($ret) > 0) {
        while($row = mysqli_fetch_assoc($ret)) {
            echo <<<EOL
            <option value="$row[name]">$row[name]</option>
EOL;
        }
    }
    echo <<<EOL
    <input type='submit' name='submit' value="Supprimer la categorie"/>
    </select>
    </form>
EOL;
?>
  </fieldset>
</form>


<h2>Gérer les utilisateurs</h2>
<form method="post" id="add_account">
  <fieldset>
    <h3>Ajouter un compte</h3>
    <label for="fname">Prénom : </label><input id="fname" name="fname" type="text"/><br/>
    <label for="lname">Nom : </label><input id="lname" name="lname" type="text"/><br/>
    <label for="email">E-mail : </label><input id="email" name="email" type="email"/><br/>
    <label for="passwd">Mot de passe : </label><input id="passwd" type="password" name="passwd"/><br/>
    <label for="address">Adresse : </label><input id="address" name ="address" type="text"/><br/>
    <label for="city">Ville : </label><input id="city" name ="city" type="text"/><br/>
    <label for="postal_code">Code postal : </label><input id="postal_code" name="postal_code" type="text"/><br/>
    <label for="phone">Téléphone : </label><input id="phone" name="phone" type="tel"/><br/>
    <input type="submit" class="submit" name="submit" value="Ajouter un compte" ><br/>
  </fieldset>
</form>

<form method="post" id="change_account">
  <fieldset>
  <h3>Modifier un compte</h3>      
  <?php      
    $ret = query("SELECT email FROM `user`");
    echo <<<EOL
        <form method="post">
        <select name="email">
            <option value="all">Aucune</option>
EOL;
    if (mysqli_num_rows($ret) > 0) {
        while($row = mysqli_fetch_assoc($ret)) {
            echo <<<EOL
            <option value="$row[email]">$row[email]</option>
EOL;
        }
    }
    echo <<<EOL
    <input type='submit' name='submit' value="valider"/>
    </select>
    </form>
EOL;
?>
<?php
    if (isset($_POST['email']) && $_POST['email'] !== "")
        $ret = query("SELECT * FROM `user` WHERE email = '$_POST[email]'");
          if (mysqli_num_rows($ret) > 0) {
            while($row = mysqli_fetch_assoc($ret)) {
              echo <<<EOL
              </br>
            <label for="fname">Prénom : </label><input id="fname" name="fname" type="text" value="$row[fname]"/><br/>
            <label for="lname">Nom : </label><input id="lname" name="lname" type="text" value="$row[lname]"/><br/>
            <label for="email">E-mail : </label><input id="email" name="email" type="text" value="$row[email]"/><br/>
            <label for="oldpw">Ancien mot de passe : </label><input id="oldpw" type="password" name="oldpw"/><br/>
            <label for="newpw">Nouveau mot de passe : </label><input id="newpw" type="password" name="newpw"/><br/>
            <label for="address">Adresse : </label><input id="address" name ="address" type="text" value="$row[address]"/><br/>
            <label for="city">Ville : </label><input id="city" name ="city" type="text" value="$row[city]"/><br/>
            <label for="postal_code">Code postal : </label><input id="postal_code" name="postal_code" type="text" value="$row[postal_code]"/><br/>
            <label for="phone">Téléphone : </label><input id="phone" name="phone" type="tel" value="$row[phone]"/><br/>
            <input type="submit" class="submit" name="submit" value="Modifier le compte" ><br/>
EOL;
           }
        }
?>
  </fieldset>
</form>

<form method="post" id="delete_account">
  <fieldset>
  <h3>Supprimer un compte</h3>      
  <?php      
    $ret = query("SELECT lname FROM `user`");
    echo <<<EOL
        <form method="post">
        <select name="lname">
            <option value="all">Aucune</option>
EOL;
    if (mysqli_num_rows($ret) > 0) {
        while($row = mysqli_fetch_assoc($ret)) {
            echo <<<EOL
            <option value="$row[lname]">$row[lname]</option>
EOL;
        }
    }
    echo <<<EOL
    <input type='submit' name='submit' value="Supprimer le compte"/>
    </select>
    </form>
EOL;
?>
  </fieldset>
</form>