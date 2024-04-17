<?php
/**
 * Created by PhpStorm.
 * User: Fabian
 * Date: 29.07.16
 * Time: 06:27
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */
    'overview'  => 'Overview',
    'addnew'    => 'Adicionar Novo',
    'backups'   => 'Backups',
    'upload'    => 'Importar',

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    */
    'title'     => 'Gestor .env',

    /*
    |--------------------------------------------------------------------------
    | View overview
    |--------------------------------------------------------------------------
    */
    'overview_title'                => 'Ficheiro .env atual',
    'overview_text'                 => 'Here you can see the content of your current active .env.<br>Click <strong>load</strong> to show the content.',
    'overview_button'               => 'Carregar',
    'overview_table_key'            => 'Attributo',
    'overview_table_value'          => 'Valor',
    'overview_table_options'        => 'Opções',
    'overview_table_popover_edit'   => 'Editar registo',
    'overview_table_popover_delete' => 'Apagar registo',
    'overview_delete_modal_text'    => 'Do you really want to delete this entry?',
    'overview_delete_modal_yes'     => 'Sim, eliminar registo',
    'overview_delete_modal_no'      => 'Não',
    'overview_edit_modal_title'     => 'Editar registo',
    'overview_edit_modal_save'      => 'Gravar',
    'overview_edit_modal_quit'      => 'Cancelar',
    'overview_edit_modal_value'     => 'Novo valor',
    'overview_edit_modal_key'       => 'Atributo',

    /*
    |--------------------------------------------------------------------------
    | View add new
    |--------------------------------------------------------------------------
    */
    'addnew_title'      => 'Adicionar new key-value-pair',
    'addnew_text'       => 'Here you can add a new key-value-pair to your <strong>current</strong> .env-file.<br>To be sure, create a backup before under the backup-tab.',
    'addnew_label_key'  => 'Atributo',
    'addnew_label_value'=> 'Valor',
    'addnew_button_add' => 'Adicionar',

    /*
    |--------------------------------------------------------------------------
    | View backup
    |--------------------------------------------------------------------------
    */
    'backup_title_one'              => 'Gravar o ficheiro atual',
    'backup_create'                 => 'Criar Backup',
    'backup_download'               => 'Download ficheiro atual',
    'backup_title_two'              => 'Backups disponíveis',
    'backup_restore_text'           => 'Here you can restore one of your available backups.',
    'backup_restore_warning'        => 'This overwrites your active .env! Be sure to backup your currently active .env-file!',
    'backup_no_backups'             => 'You have no backups available.',
    'backup_table_nr'               => 'Nº.',
    'backup_table_date'             => 'Data',
    'backup_table_options'          => 'Opções',
    'backup_table_options_show'     => 'Prévisualizar Backup',
    'backup_table_options_restore'  => 'Restaurar esta versão',
    'backup_table_options_download' => 'Download this version',
    'backup_table_options_delete'   => 'Apagar esta versão',
    'backup_modal_title'            => 'Prévisualização de Backup',
    'backup_modal_key'              => 'Atributo',
    'backup_modal_value'            => 'Valor',
    'backup_modal_close'            => 'Fechar',
    'backup_modal_restore'          => 'Restaurar',
    'backup_modal_delete'           => 'Apagar',

    /*
    |--------------------------------------------------------------------------
    | View upload
    |--------------------------------------------------------------------------
    */
    'upload_title'  => 'Carregar ficheiro',
    'upload_text'   => 'Carregue um ficheiro .env do computador.',
    'upload_warning'=> '<strong>AVISO:</strong> Esta operação substituirá o ficheiro .env ativo atualmente. Certifique-se de criar um backup antes de fazer o upload.',
    'upload_label'  => 'Selecionar ficheiro',
    'upload_button' => 'Carregar',

    /*
    |--------------------------------------------------------------------------
    | Messages from View
    |--------------------------------------------------------------------------
    */
    'new_entry_added'   => 'O novo atributo foi adicionado com sucesso ao ficheiro atual.',
    'entry_edited'      => 'O atributo foi editado com sucesso.',
    'entry_deleted'     => 'Atributo eliminado com sucesso.',
    'delete_entry'      => 'Eliminar atributo',
    'backup_created'    => 'Backup criado com sucesso.',
    'backup_restored'   => 'Backup restaurado com sucesso.',

    /*
    |--------------------------------------------------------------------------
    | Messages from Controller
    |--------------------------------------------------------------------------
    */
    'controller_backup_deleted' => 'Backup eliminado com sucesso.',
    'controller_backup_created' => 'Backup criado com sucesso.'
];
