<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-account" data-toggle="tooltip" title="<?php echo $button_save; ?>"
                        class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>"
                   class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-account"
                      class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-ecomail_api_key"><?php echo $entry_api_key; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="ecomail_api_key" value="<?php echo $ecomail_api_key; ?>"
                                   placeholder="<?php echo $entry_api_key; ?>" id="input-ecomail_api_key" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-ecomail_list_id"><?php echo $entry_list_id; ?></label>
                        <div class="col-sm-10">
                            <select name="ecomail_list_id" id="input-ecomail_list_id" class="form-control">
                                <?php foreach ($optionsListId as $list) { ?>
                                <?php if ($ecomail_list_id == $list['value']) { ?>
                                <option value="<?php echo $list['value']; ?>"
                                        selected="selected"><?php echo $list['label']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $list['value']; ?>"><?php echo $list['label']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                            <span>Vyberte list do kterého budou zapsáni noví zákazníci</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-ecomail_app_id"><?php echo $entry_app_id; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="ecomail_app_id" value="<?php echo $ecomail_app_id; ?>"
                                   placeholder="<?php echo $entry_app_id; ?>" id="input-ecomail_app_id" class="form-control"/>
                            <span>Tento údaj slouží pro aktivaci funkce Trackovací kód</span>
                        </div>
                    </div>
                    <?php /*
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-10">
                            <select name="ecomail_status" id="input-status" class="form-control">
                                <?php if ($ecomail_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    */ ?>
                </form>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>