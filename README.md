# CodeIgniter DataTables

DataTables server-side for CodeIgniter, supported for both CodeIgniter 3 and CodeIgniter 4.

**Note:** This library only handle the server-side part, you will still need to set up the client-side components, such as jQuery, the DataTables library, and the necessary styles. **Don't worry, we've included examples below to help you get started.**

## Requirements

No additional requirements are needed if you are already using CodeIgniter. Simply integrate this library into your existing project.

## Installation

To install the library, use Composer. This command will handle the installation process for you:

```sh
composer require ngekoding/codeigniter-datatables
```

## Usage

Below is a basic example of how to use this library. Feel free to customize the client-side configuration, such as defining searchable columns, orderable columns, and other DataTables options.

The usage of this library is similar for both CodeIgniter 3 and CodeIgniter 4. **The primary difference is in how you create the query builder.** Below are examples for both versions.

### CodeIgniter 3 Example

```php
// CodeIgniter 3 Example

// Here we will select all fields from posts table
// and make a join with categories table
// IMPORTANT! We don't need to call ->get() here
$queryBuilder = $this->db->select('p.*, c.name category')
                    ->from('posts p')
                    ->join('categories c', 'c.id=p.category_id');

// The library will automatically detect the CodeIgniter version you are using
$datatables = new Ngekoding\CodeIgniterDataTables\DataTables($queryBuilder);
$datatables->generate(); // done
```

### CodeIgniter 4 Example

```php
// CodeIgniter 4 Example

$db = db_connect();
$queryBuilder = $db->from('posts p')
                   ->select('p.*, c.name category')
                   ->join('categories c', 'c.id=p.category_id');

// The library will automatically detect the CodeIgniter version you are using
$datatables = new Ngekoding\CodeIgniterDataTables\DataTables($queryBuilder);
$datatables->generate(); // done
```

**The above examples will give you for [ajax data source (arrays)](https://datatables.net/examples/ajax/simple.html), so you need to make sure the table header you makes for the client side is match with the ajax response. We will talk about the objects data source below.**

### Client Side Examples

You must include the jQuery and DataTables library.

```html
<link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">

<table id="table-post" class="display" width="100%">
  <thead>
    <th>ID</th>
    <th>Title</th>
    <th>Category</th>
    <th>Description</th>
  </thead>
</table>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
$('#table-post').DataTable({
  processing: true,
  serverSide: true,
  ajax: {
    url: 'http://localhost/project/index.php/post/ajax_datatables', // Change with your own
    method: 'GET', // You are freely to use POST or GET
  }
})
</script>
```

## Objects Data Source

As was mentioned above, the default data source we get is an arrays. It is easy also to get the objects data source.

To get objects response, you just need to call `asObject()` method.

```php
$datatables->asObject()
           ->generate();
```

And then you can configure the client side with columns option to fit your data.

```js
$('#table-post').DataTable({
  processing: true,
  serverSide: true,
  ajax: {
    url: 'http://localhost/project/index.php/post/ajax_datatables',
    method: 'GET',
  },
  columns: [
    { data: 'id' },
    { data: 'title' },
    { data: 'category' },
    { data: 'description' }
  ]
})
</script>
```

## Some Others Settings

Some basic functionalities already available, here is the full settings you can doing to this library.

### Use class for spesify the CodeIgniter version
```php
// General, use the second param to define the version (3 or 4)
$datatables = new Ngekoding\CodeIgniterDataTables\DataTables($queryBuilder, 3);

// CodeIgniter 3
$datatables = new Ngekoding\CodeIgniterDataTables\DataTablesCodeIgniter3($queryBuilder);

// CodeIgniter 4
$datatables = new Ngekoding\CodeIgniterDataTables\DataTablesCodeIgniter4($queryBuilder);

```

### Available Options

```php
$datatables = new Ngekoding\CodeIgniterDataTables\DataTables($queryBuilder);

// Return the output as objects instead of arrays
$datatables->asObject();

// Only return title & category (accept string or array)
$datatables->only(['title', 'category']);

// Return all except the id
// You may use one of only or except
$datatables->except(['id']);

// Format the output
$datatables->format('title', function($value, $row) {
  return '<b>'.$value.'</b>';
});

// Add extra column
$datatables->addColumn('action', function($row) {
  return '<a href="url/to/delete/post/'.$row->id.'">Delete</a>';
});

// Add column alias
// It is very useful when we use SELECT JOIN to prevent column ambiguous
$datatables->addColumnAlias('p.id', 'id');

// Add column aliases
// Same as the addColumnAlias, but for multiple alias at once
$datatables->addColumnAliases([
  'p.id' => 'id',
  'c.name' => 'category'
]);

// Add squence number
// The default key is `sequenceNumber`
// You can change it with give the param
$datatables->addSequenceNumber();
$datatables->addSequenceNumber('rowNumber'); // It will be rowNumber

// Don't forget to call generate to get the results
$datatables->generate();
```

## Complete Example

I already use this library to the existing project with completed CRUD operations, you can found it [here](https://github.com/ngekoding/ci-crud). 

<details>
<summary>Please look at these files:</summary>

- application/composer.json
- application/controllers/Post.php
- application/models/M_post.php
- application/views/template.php
- application/views/posts/index-datatables.php
- application/views/posts/index-datatables-array.php
- application/helpers/api_helper.php
- assets/js/custom.js

</details>