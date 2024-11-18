<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="adminsite.css">
</head>
<body>
    <div class="container">
        <form action="add_product.php" method="post"  enctype="multipart/form-data"> 
            <div class="head">PRODUCT DETAILS</div>
            <div class="input-group">
                <label>Product Name:</label>
                <input type="text" name="name" placeholder="Enter Product name" >
                <span id="nameerror"></span>
            </div>
            <div class="input-group">
                <label>Product Image:</label>
                <input type="file" name="my_image" required>
                <span id="imageerror">Types: jpg,jpeg,png</span>
            </div>
            <div class="input-group">
                <label>Price:</label>
                <input type="text" name="price" placeholder="Enter Price" >
                <span id="doberror"></span>
            </div>
            <div class="input-group add">
                <label>Description:</label>
                <textarea type="textarea" name="description" placeholder="Enter Description" rows="10" columns="20"></textarea>
                <span id="error">Limit 200 letters</span>
            </div>
            <div class="input-group">
                <label>Category:</label>
                <input type="text" name="category" placeholder="Enter Category" >
                <span id="aderror"></span>
                <span id="adcrt"></span>
            </div>
            <div class="input-group">
                <label>Stock:</label>
                <input group="text" name="stock" placeholder="Enter Stock quantity">
                <span id="aualierror"></span>
            </div>
            <div class="buttons">
            <button type="submit" name="submit">Submit</button>
            <span id="submiterror"></span>
            </div>
        </form>
    </div>
</body>
</html>