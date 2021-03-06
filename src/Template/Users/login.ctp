<!-- File: src/Template/Users/login.ctp -->
<div class="users form columns has-text-centered is-centered">
    <?php
    echo $this->Form->create();
    ?>
    <h1 style="margin-top: 20%" class="subtitle is-centered notification is-info"> Γραμματεία 180 MK/B HAWK </h1>
    <?php
    echo $this->Form->control('username', [
        'label' => 'Όνομα χρήστη',
        'templateVars' => [
            'icon' => 'fas fa-user'
        ]
    ]);
    echo $this->Form->control('password', [
        'label' => 'Κωδικός',
        'templateVars' => [
            'icon' => 'fas fa-lock'
        ]
    ]);
    echo $this->Form->button(__('Σύνδεση'));
    echo $this->Form->end()
    ?>
</div>

<style>
    body {
        font-size: large;
    }
</style>
