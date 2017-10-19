<%
/**
 * Bootstrap Looks
 * @copyright     Agus Sigit Wisnubroto
 * @link          http://github.com/aswzen
 * @since         0.0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Utility\Inflector;

$fields = collection($fields)
    ->filter(function($field) use ($schema) {
        return $schema->columnType($field) !== 'binary';
    });

if (isset($modelObject) && $modelObject->hasBehavior('Tree')) {
    $fields = $fields->reject(function ($field) {
        return $field === 'lft' || $field === 'rght';
    });
}
%>
<nav class="navbar navbar-toggleable-md navbar-light bg-faded" id="actions-sidebar">
    <?= $this->Html->link(__('List <%= $pluralHumanName %>'), ['action' => 'index'], ['class' => 'navbar-brand']) ?>
<%
    $done = [];
    foreach ($associations as $type => $data) {
        foreach ($data as $alias => $details) {
            if ($details['controller'] !== $this->name && !in_array($details['controller'], $done)) {
%>
    <?= $this->Html->link(__('List <%= $this->_pluralHumanName($alias) %>'), ['controller' => '<%= $details['controller'] %>', 'action' => 'index'], ['class' => 'navbar-brand']) ?>    
    <?= $this->Html->link(__('New <%= $this->_singularHumanName($alias) %>'), ['controller' => '<%= $details['controller'] %>', 'action' => 'add'], ['class' => 'navbar-brand']) ?>
<%
                $done[] = $details['controller'];
            }
        }
    }
%>
</nav>

<div class="panel panel-default form-panel <%= $pluralVar %>">
    <div class="panel-heading"><?= __('<%= Inflector::humanize($action) %> <%= $singularHumanName %>') ?></div>
    <div class="panel-body">
    	<?= $this->Form->create($<%= $singularVar %>) ?>
        <?php
<%
        foreach ($fields as $field) {
            if (in_array($field, $primaryKey)) {
                continue;
            }
            if (isset($keyFields[$field])) {
                $fieldData = $schema->column($field);
                if (!empty($fieldData['null'])) {
%>
            echo $this->Form->control('<%= $field %>', ['options' => $<%= $keyFields[$field] %>, 'empty' => true, 'class' => 'form-control', 'label' => '<%= ucwords(str_replace("_", " ", $field)) %>']);
<%
                } else {
%>
            echo $this->Form->control('<%= $field %>', ['options' => $<%= $keyFields[$field] %>, 'class' => 'form-control', 'label' => '<%= ucwords(str_replace("_", " ", $field)) %>']);
<%
                }
                continue;
            }
            if (!in_array($field, ['created', 'modified', 'updated'])) {
                $fieldData = $schema->column($field);
                if (in_array($fieldData['type'], ['date', 'datetime', 'time']) && (!empty($fieldData['null']))) {
%>
            echo $this->Form->control('<%= $field %>', ['empty' => true, 'class' => 'form-control', 'label' => '<%= ucwords(str_replace("_", " ", $field)) %>']);
<%
                } else {
%>
            echo $this->Form->control('<%= $field %>', ['class' => 'form-control', 'label' => '<%= ucwords(str_replace("_", " ", $field)) %>']);
<%
                }
            }
        }
        if (!empty($associations['BelongsToMany'])) {
            foreach ($associations['BelongsToMany'] as $assocName => $assocData) {
%>
            echo $this->Form->control('<%= $assocData['property'] %>._ids', ['options' => $<%= $assocData['variable'] %>, 'class' => 'form-control', 'label' => '<%= ucwords(str_replace("_", " ", $field)) %>']);
<%
            }
        }
%>
        ?>
        <div class="submit">
            <input class="btn btn-primary" type="submit" value="Submit">
        </div>
	    <?= $this->Form->end() ?>
	</div>
</div>