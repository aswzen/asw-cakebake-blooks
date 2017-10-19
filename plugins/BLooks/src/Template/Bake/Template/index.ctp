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
  * @var \<%= $namespace %>\Model\Entity\<%= $entityClass %>[]|\Cake\Collection\CollectionInterface $<%= $pluralVar %>
  */
?>
<%
use Cake\Utility\Inflector;

$fields = collection($fields)
    ->filter(function($field) use ($schema) {
        return !in_array($schema->columnType($field), ['binary', 'text']);
    });

if (isset($modelObject) && $modelObject->behaviors()->has('Tree')) {
    $fields = $fields->reject(function ($field) {
        return $field === 'lft' || $field === 'rght';
    });
}

if (!empty($indexColumns)) {
    $fields = $fields->take($indexColumns);
}

%>
<nav class="navbar navbar-toggleable-md navbar-light bg-faded" id="actions-sidebar">
    <?= $this->Html->link(__('New <%= $singularHumanName %>'), ['action' => 'add'], ['class' => 'navbar-brand']) ?>
<%
    $done = [];
    foreach ($associations as $type => $data):
        foreach ($data as $alias => $details):
            if (!empty($details['navLink']) && $details['controller'] !== $this->name && !in_array($details['controller'], $done)):
%>
    <?= $this->Html->link(__('List <%= $this->_pluralHumanName($alias) %>'), ['controller' => '<%= $details['controller'] %>', 'action' => 'index'], ['class' => 'navbar-brand']) ?>
    <?= $this->Html->link(__('New <%= $this->_singularHumanName($alias) %>'), ['controller' => '<%= $details['controller'] %>', 'action' => 'add'], ['class' => 'navbar-brand']) ?>
<%
                $done[] = $details['controller'];
            endif;
        endforeach;
    endforeach;
%>
</nav>

<div class="panel panel-default <%= $pluralVar %>">
    <div class="panel-heading"><?= __('<%= $pluralHumanName %>') ?></div>
    <div class="panel-body">
        <table class="table table-striped">
            <thead>
                <tr>
    <% foreach ($fields as $field): %>
                    <th scope="col"><?= $this->Paginator->sort('<%= $field %>') ?></th>
    <% endforeach; %>
                    <th scope="col" class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($<%= $pluralVar %> as $<%= $singularVar %>): ?>
                <tr>
    <%        foreach ($fields as $field) {
                $isKey = false;
                if (!empty($associations['BelongsTo'])) {
                    foreach ($associations['BelongsTo'] as $alias => $details) {
                        if ($field === $details['foreignKey']) {
                            $isKey = true;
    %>
                <td><?= $<%= $singularVar %>->has('<%= $details['property'] %>') ? $this->Html->link($<%= $singularVar %>-><%= $details['property'] %>-><%= $details['displayField'] %>, ['controller' => '<%= $details['controller'] %>', 'action' => 'view', $<%= $singularVar %>-><%= $details['property'] %>-><%= $details['primaryKey'][0] %>]) : '' ?></td>
    <%
                            break;
                        }
                    }
                }
                if ($isKey !== true) {
                    if (!in_array($schema->columnType($field), ['integer', 'biginteger', 'decimal', 'float'])) {
    %>
                <td><?= h($<%= $singularVar %>-><%= $field %>) ?></td>
    <%
                    } else {
    %>
                <td><?= $this->Number->format($<%= $singularVar %>-><%= $field %>) ?></td>
    <%
                    }
                }
            }

            $pk = '$' . $singularVar . '->' . $primaryKey[0];
    %>
                <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', <%= $pk %>], ['class' => 'btn btn-primary']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', <%= $pk %>], ['class' => 'btn btn-primary']) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', <%= $pk %>], ['confirm' => __('Are you sure you want to delete # {0}?', <%= $pk %>), 'class' => 'btn btn-danger']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="paginator">
            <ul class="pagination">
                <?= $this->Paginator->first('◄◄ ' . __('First')) ?>
                <?= $this->Paginator->prev('◄ ' . __('Previous')) ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next(__('Next') . ' ►') ?>
                <?= $this->Paginator->last(__('Last') . ' ►►') ?>
            </ul>
            <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
        </div>
    </div>
</div>
