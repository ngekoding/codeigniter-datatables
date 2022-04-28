# CodeIgniter DataTables

DataTables server-side for CodeIgniter, supported both for CodeIgniter 3 and CodeIgniter 4.

**Note:** This library only handle the server-side part, you still needs to configure the client side like jQuery, DataTables library and including the styles.

## Requirements

The requirements is base on what version of CodeIgniter you use.
- [CodeIgniter 3 Requirements](https://codeigniter.com/userguide3/general/requirements.html)
- [CodeIgniter 4 Requirements](https://codeigniter.com/user_guide/intro/requirements.html)

## Installation

You just need to use composer and everything is done.

```sh
composer require ngekoding/codeigniter-datatables
```

## Usage

Here is the basic example to use this library, you are freely make any changes for the client side, like defining searchable column, orderable column, etc...

### CodeIgniter 3 Example

```php
// CodeIgniter 3 Example

// Here we will select all fields from posts table
// and make a join with categories table
// Please note: we don't need to call ->get() here
$queryBuilder = $this->db->select('p.*, c.name category')
                    ->from('posts p')
                    ->join('categories c', 'c.id=p.category_id');

/**
 * The first parameter is the query builder instance
 * and the second is the codeigniter version (3 or 4) 
 */
$datatables = new Ngekoding\CodeIgniterDataTables\DataTables($queryBuilder, '3');
$datatables->generate(); // done
```

### CodeIgniter 4 Example

```php
// CodeIgniter 4 Example

$db = db_connect();
$queryBuilder = $db->from('posts p')
                   ->select('p.*, c.name category')
                   ->join('categories c', 'c.id=p.category_id');

$datatables = new Ngekoding\CodeIgniterDataTables\DataTables($queryBuilder, '4');
$datatables->generate(); // done
```

**The above examples will give you for [ajax data source (arrays)](https://datatables.net/examples/ajax/simple.html), so you need to make sure the table header you makes for the client side is match with the ajax response. We will talk about the objects data source below.**

### Client Side Examples

You must include the jQuery and DataTables library.

```html
<link src="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<table id="table-post">
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
// General, use the second param to define the version
// The default is 4
$datatables = new Ngekoding\CodeIgniterDataTables\DataTables($queryBuilder, '3');

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

// Add squence number
// The default key is `sequenceNumber`
// You can change it with give the param
$datatables->addSequenceNumber();
$datatables->addSequenceNumber('rowNumber'); // It will be rowNumber

// Don't forget ot call generate to get the results
$datatables->generate();
```