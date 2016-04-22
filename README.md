# Fork CMS Filter
## Introduction
Module created for making filtering process easier. I saw that Fork CMS lacks of filtering functionality so decided
to create one.

## Requirements
* Core: Fork CMS 3.9.4

## Usage
### Action files
#### Namespaces
First of all we need to load proper namespaces

```
use Common\Modules\Localization\Entity;
```

#### Examples
I will add some examples, how to use this thing.

##### Initializing filter

```
$this->filter = new Filter();
$this->filter
    ->addTextCriteria(
        'search',
        array('p.email', 'm.first_name', 'm.last_name', 'ma.company', 'ma.company_code'),
        CommonFilterHelper::OPERATOR_PATTERN
    )
    ->addDropdownCriteria('status', array('p.status'), BackendProfilesModel::getStatusForDropDown());
```

##### Getting data
Just pass your DataGrid query into filters getQuery method and receive filtered data.

```
$this->dgMembers = new BackendDataGridDB($this->filter->getQuery(MembersModel::QRY_DG_MEMBERS));
```

##### Parsing filter into a template
For filter to make visible you will need to parse everything into a template

```
$this->filter->parse($this->tpl);
```

##### Template example
Everything is just like simple form.

```
{form:filter}
  <div class="panel panel-default">
    <div class="panel-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="search">{$lblSearch|ucfirst}</label>
            {$txtSearch} {$txtSearchError}
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="status">{$lblStatus|ucfirst}</label>
            {$ddmStatus} {$ddmStatusError}
          </div>
        </div>
      </div>
    </div>
    <div class="panel-footer">
      <div class="btn-toolbar">
        <div class="btn-group pull-right">
          <button type="submit" class="btn btn-primary">
            <span class="glyphicon glyphicon-refresh"></span>&nbsp;
            {$lblUpdateFilter|ucfirst}
          </button>
        </div>
      </div>
    </div>
  </div>
{/form:filter}
```

## Issues
If you are having any issues, please create issue at [Github](https://github.com/vytenizs/forkcms-module-filter/issues).
Or contact me directly. Thank you.

## Contacts

* e-mail: info@vytsci.lt
* skype: vytenizs
