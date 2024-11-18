<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <form action="validatecus.php" method="post"> 
            <div class="head">REGISTER</div>
            <div class="input-group">
                <label>Name:</label>
                <input type="text" id="name" name="name" placeholder="Enter name" required>
            </div>
            <div class="input-group">
                <label>E-mail Id:</label>
                <input type="email" id="mail" name="mail" placeholder="Enter Email ID" required>
            </div>
            <div class="input-group add">
                <label>Address:</label>
                <textarea rows="10" columns="50" id="ad" name="ad" placeholder="Enter your address" required></textarea>
            </div>
            <div class="input-group">
                <label>Contact Number:</label>
                <input type="number" id="contact" name="contact" required>
            </div>
            <div class="input-group">
                <label>Password:</label>
                <input type="password" id="pw" name="pw" required>
            </div>
            <div class="input-group">
                <label>Confirm Password:</label>
                <input type="password" id="confirm_pw" name="confirm_pw" required>
            </div>
            <div class="buttons">
                <button type="submit">Submit</button>
            </div>
        </form>
    </div>
</body>
</html>
