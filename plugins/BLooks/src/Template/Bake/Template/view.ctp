<%
/**
 * Bootstrap Looks
 * @copyright     Agus Sigit Wisnubroto
 * @link          http://github.com/aswzen
 * @since         0.0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
%>
<?php
/**
  * @var \<%= $namespace %>\View\AppView $this
  * @var \<%= $namespace %>\Model\Entity\<%= $entityClass %> $<%= $singularVar %>
  */
?>
<%
use Cake\Utility\Inflector;

$associations += ['BelongsTo' => [], 'HasOne' => [], 'HasMany' => [], 'BelongsToMany' => []];
$immediateAssociations = $associations['BelongsTo'];
$associationFields = collection($fields)
    ->map(function($field) use ($immediateAssociations) {
        foreach ($immediateAssociations as $alias => $details) {
            if ($field === $details['foreignKey']) {
                return [$field => $details];
            }
        }
    })
    ->filter()
    ->reduce(function($fields, $value) {
        return $fields + $value;
    }, []);

$groupedFields = collection($fields)
    ->filter(function($field) use ($schema) {
        return $schema->columnType($field) !== 'binary';
    })
    ->groupBy(function($field) use ($schema, $associationFields) {
        $type = $schema->columnType($field);
        if (isset($associationFields[$field])) {
            return 'string';
        }
        if (in_array($type, ['integer', 'float', 'decimal', 'biginteger'])) {
            return 'number';
        }
        if (in_array($type, ['date', 'time', 'datetime', 'timestamp'])) {
            return 'date';
        }
        return in_array($type, ['text', 'boolean']) ? $type : 'string';
    })
    ->toArray();

$groupedFields += ['number' => [], 'string' => [], 'boolean' => [], 'date' => [], 'text' => []];
$pk = "\$$singularVar->{$primaryKey[0]}";
%>
<nav class="navbar navbar-toggleable-md navbar-light bg-faded" id="actions-sidebar">
    <?= $this->Html->link(__('Edit <%= $singularHumanName %>'), ['action' => 'edit', <%= $pk %>], ['class' => 'navbar-brand']) ?> 
    <?= $this->Form->postLink(__('Delete <%= $singularHumanName %>'), ['action' => 'delete', <%= $pk %>], ['confirm' => __('Are you sure you want to delete # {0}?', <%= $pk %>), 'class' => 'navbar-brand']) ?> 
    <?= $this->Html->link(__('List <%= $pluralHumanName %>'), ['action' => 'index'], ['class' => 'navbar-brand']) ?> 
    <?= $this->Html->link(__('New <%= $singularHumanName %>'), ['action' => 'add'], ['class' => 'navbar-brand']) ?> 
<%
$done = [];
foreach ($associations as $type => $data) {
    foreach ($data as $alias => $details) {
        if ($details['controller'] !== $this->name && !in_array($details['controller'], $done)) {
%>
    <?= $this->Html->link(__('List <%= $this->_pluralHumanName($alias) %>'), ['controller' => '<%= $details['controller'] %>', 'action' => 'index'], ['class' => 'navbar-brand']) ?> 
    <?= $this->Html->link(__('New <%= Inflector::humanize(Inflector::singularize(Inflector::underscore($alias))) %>'), ['controller' => '<%= $details['controller'] %>', 'action' => 'add'], ['class' => 'navbar-brand']) ?> 
<%
            $done[] = $details['controller'];
        }
    }
}
%>
</nav>

<div class="panel panel-default <%= $pluralVar %>">
    <div class="panel-heading"><?= h($<%= $singularVar %>-><%= $displayField %>) ?></div>
    <div class="panel-body">
        <table class="table table-striped">
<% if ($groupedFields['string']) : %>
<% foreach ($groupedFields['string'] as $field) : %>
<% if (isset($associationFields[$field])) :
            $details = $associationFields[$field];
%>
            <tr>
                <th scope="row"><?= __('<%= Inflector::humanize($details['property']) %>') ?></th>
                <td><?= $<%= $singularVar %>->has('<%= $details['property'] %>') ? $this->Html->link($<%= $singularVar %>-><%= $details['property'] %>-><%= $details['displayField'] %>, ['controller' => '<%= $details['controller'] %>', 'action' => 'view', $<%= $singularVar %>-><%= $details['property'] %>-><%= $details['primaryKey'][0] %>]) : '' ?></td>
            </tr>
    <% else : %>
            <tr>
                <th scope="row"><?= __('<%= Inflector::humanize($field) %>') ?></th>
                <td><?= h($<%= $singularVar %>-><%= $field %>) ?></td>
            </tr>
    <% endif; %>
    <% endforeach; %>
    <% endif; %>
    <% if ($associations['HasOne']) : %>
        <%- foreach ($associations['HasOne'] as $alias => $details) : %>
            <tr>
                <th scope="row"><?= __('<%= Inflector::humanize(Inflector::singularize(Inflector::underscore($alias))) %>') ?></th>
                <td><?= $<%= $singularVar %>->has('<%= $details['property'] %>') ? $this->Html->link($<%= $singularVar %>-><%= $details['property'] %>-><%= $details['displayField'] %>, ['controller' => '<%= $details['controller'] %>', 'action' => 'view', $<%= $singularVar %>-><%= $details['property'] %>-><%= $details['primaryKey'][0] %>]) : '' ?></td>
            </tr>
        <%- endforeach; %>
    <% endif; %>
    <% if ($groupedFields['number']) : %>
    <% foreach ($groupedFields['number'] as $field) : %>
            <tr>
                <th scope="row"><?= __('<%= Inflector::humanize($field) %>') ?></th>
                <td><?= $this->Number->format($<%= $singularVar %>-><%= $field %>) ?></td>
            </tr>
    <% endforeach; %>
    <% endif; %>
    <% if ($groupedFields['date']) : %>
    <% foreach ($groupedFields['date'] as $field) : %>
            <tr>
                <th scope="row"><%= "<%= __('" . Inflector::humanize($field) . "') %>" %></th>
                <td><?= h($<%= $singularVar %>-><%= $field %>) ?></td>
            </tr>
    <% endforeach; %>
    <% endif; %>
    <% if ($groupedFields['boolean']) : %>
    <% foreach ($groupedFields['boolean'] as $field) : %>
            <tr>
                <th scope="row"><?= __('<%= Inflector::humanize($field) %>') ?></th>
                <td><?= $<%= $singularVar %>-><%= $field %> ? __('Yes') : __('No'); ?></td>
            </tr>
    <% endforeach; %>
    <% endif; %>
    <% if ($groupedFields['text']) : %>
    <% foreach ($groupedFields['text'] as $field) : %>
            <tr>
                <th scope="row"><?= __('<%= Inflector::humanize($field) %>') ?></th>
                <td><?= $this->Text->autoParagraph(h($<%= $singularVar %>-><%= $field %>)); ?></td>
            </tr>
    <% endforeach; %>
    <% endif; %>
        </table>
    <%
    $relations = $associations['HasMany'] + $associations['BelongsToMany'];
    foreach ($relations as $alias => $details):
        $otherSingularVar = Inflector::variable($alias);
        $otherPluralHumanName = Inflector::humanize(Inflector::underscore($details['controller']));
        %>
        <div class="related">
            <h4><?= __('Related <%= $otherPluralHumanName %>') ?></h4>
            <?php if (!empty($<%= $singularVar %>-><%= $details['property'] %>)): ?>
            <table class="table table-striped">
                <tr>
    <% foreach ($details['fields'] as $field): %>
                    <th scope="col"><?= __('<%= Inflector::humanize($field) %>') ?></th>
    <% endforeach; %>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
                <?php foreach ($<%= $singularVar %>-><%= $details['property'] %> as $<%= $otherSingularVar %>): ?>
                <tr>
                <%- foreach ($details['fields'] as $field): %>
                    <td><?= h($<%= $otherSingularVar %>-><%= $field %>) ?></td>
                <%- endforeach; %>
                <%- $otherPk = "\${$otherSingularVar}->{$details['primaryKey'][0]}"; %>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['controller' => '<%= $details['controller'] %>', 'action' => 'view', <%= $otherPk %>], ['class' => 'btn btn-default']) ?>
                        <?= $this->Html->link(__('Edit'), ['controller' => '<%= $details['controller'] %>', 'action' => 'edit', <%= $otherPk %>], ['class' => 'btn btn-default']) ?>
                        <?= $this->Form->postLink(__('Delete'), ['controller' => '<%= $details['controller'] %>', 'action' => 'delete', <%= $otherPk %>], ['confirm' => __('Are you sure you want to delete # {0}?', <%= $otherPk %>), 'class' => 'btn btn-default']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php endif; ?>
        </div>
    <% endforeach; %>
    </div>
</div>
