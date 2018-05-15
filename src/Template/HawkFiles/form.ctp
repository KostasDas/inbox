

<section class="hawkFiles section">
    <?= $this->Element('Buttons/back') ?>
    <div class="columns is-centered">
        <?= $this->Form->create($hawkFile, ['enctype' => 'multipart/form-data']) ?>
        <h3 class="title is-3">Εισαγωγή εισερχομένου</h3>
        <div class="column">
            <?php
            echo $this->Form->control('number', [
                'label'        => 'Αριθμός ταυτότητας',
                'templateVars' => [
                    'icon' => 'fa-id-card',
                ],
            ]);
            echo $this->Form->control('topic', [
                'label'        => 'Θέμα',
                'templateVars' => [
                    'icon' => 'fa-comment',
                ],
            ]);

            echo $this->Form->control('protocol', [
                'label'        => 'Φ/SIC',
                'templateVars' => [
                    'icon' => 'fa-bars',
                ],
                'default'      => '',
            ]);
            echo $this->Form->control('type', [
                'label'        => 'Τύπος',
                'options' => ['new' => 'Άλλο'] + $types->toArray(),
                'empty' => 'Εισάγετε τύπο',
            ]);
            echo $this->Form->control('sender', [
                'label'        => 'Αποστολέας',
                'options' => ['new' => 'Άλλο'] + $senders->toArray(),
                'empty' => 'Εισάγετε αποστολέα',
            ]);
            echo $this->Form->control('office', [
                'label'        => 'Υπόψιν γραφείου',
                'options' => ['new' => 'Άλλο'] + $offices->toArray(),
                'empty' => 'Εισάγετε γραφείο',
            ]);
            ?>
            <div class="file is-black has-name required" style="margin-bottom: 10px">
                <label class="file-label">
                    <input class="file-input" id="file-upload-input" type="file" name="location">
                    <span class="file-cta">
                      <span class="file-icon">
                        <i class="fas fa-upload"></i>
                      </span>
                      <span class="file-label">
                        Επιλέξτε Αρχείο
                      </span>
                    </span>
                    <span class="file-name" id="file-upload-name">
                    </span>
                </label>
            </div>
            <?php
            echo $this->Form->button(__('Εισαγωγή'))
            ?>
        </div>

        <?= $this->Form->end() ?>
    </div>
</section>
<?php
echo $this->Html->script('upload');
echo $this->Html->script('select');
?>