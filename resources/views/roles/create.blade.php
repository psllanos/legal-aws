
{{ Form::open(array('url' => 'roles')) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                {{ Form::label('name', __('Role Name'),['class'=>'col-form-label']) }}
                {{ Form::text('name', null, array('class' => 'form-control')) }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
                <div class="">
                    <div class="table-border-style">   
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th width="10%">
                                            <div class="form-check">
                                                <input type="checkbox" class="align-middle form-check-input" name="checkall"  id="checkall" >
                                            </div>
                                           
                                        </th>
                                        <th width="10%" class="text-dark">{{__('Module')}}</th>
                                        <th class="text-dark">{{__('Permissions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td width="10%" >
                                            <div class="form-check">
                                                <input type="checkbox" class="align-middle ischeck form-check-input" name="checkall" data-id="account" >
                                            </div>
                                        </td>
                                            <td width="10%"><label class="ischeck" data-id="account">{{__('Account')}}</label></td>
                                                <td>
                                                    <div class="row">
                                                        @if(in_array('System Settings',$permissions))
                                                            @php($key = array_search('System Settings', $permissions))
                                                            <div class="col-4 form-check">
                                                                {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_account','id'=>'permission_'.$key]) }}
                                                                {{ Form::label('permission_'.$key, __('System Settings'),['class'=>'form-check-labe font-weight-500']) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                            $modules = [
                                                'User',
                                                'Role',
                                                'Lead',
                                                'Deal',
                                                'Estimation',
                                                'Task',
                                                'Invoice',
                                                'Product',
                                                'Expense',
                                                'Tax',
                                                'Client',
                                                'Pipeline',
                                                'Stage',
                                                'Lead Stage',
                                                'Label',
                                                'Source',
                                                'Payment',
                                                'Expense Category',
                                                'Custom Field',
                                                'Contract Type',
                                                'Contract',
                                                'Deal Email',
                                                'Deal Call',
                                                'Lead Email',
                                                'Lead Call',
                                                'MDF',
                                                'MDF Status',
                                                'MDF Type',
                                                'MDF Sub Type',
                                            ];

                                            if(\Auth::user()->type == 'Super Admin')
                                            {
                                                $modules[] = 'Language';
                                            }

                                            ?>

                                            @foreach($modules as $module)
                                                <?php

                                                if($module == 'Expense Category')
                                                {
                                                    $s_name = 'Expense Categories';
                                                }
                                                elseif($module == 'Company')
                                                {
                                                    $s_name = 'Companies';
                                                }
                                                elseif($module == 'Tax')
                                                {
                                                    $s_name = 'Taxes';
                                                }
                                                elseif($module == 'Manage MDF Status')
                                                {
                                                    $s_name = 'MDF Status';
                                                }
                                                else
                                                {
                                                    $s_name = $module . "s";
                                                }
                                                ?>
                                                <tr>

                                                     <td width="10%">
                                                        <div class="form-check">
                                                             <input type="checkbox" class="align-middle ischeck form-check-input" name="checkall" data-id="{{str_replace(' ', '', $module)}}" >
                                                        </div>
                                                       
                                                    </td>
                                                    <td width="10%"><label class="ischeck" data-id="{{str_replace(' ', '', $module)}}">{{ ucfirst($module) }}</label></td>
                                                    <td>
                                                        <div class="row">
                                                            @if(in_array('Manage '.$s_name,$permissions))
                                                                @php($key = array_search('Manage '.$s_name, $permissions))
                                                                <div class="col-3 form-check">
                                                                    {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_'.str_replace(' ', '', $module),'id'=>'permission_'.$key]) }}
                                                                    {{ Form::label('permission_'.$key, 'Manage',['class'=>'form-check-labe font-weight-500']) }}
                                                                </div>
                                                            @endif
                                                            @if(in_array('Create '.$module,$permissions))
                                                                @php($key = array_search('Create '.$module, $permissions))
                                                                <div class="col-3 form-check">
                                                                    {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_'.str_replace(' ', '', $module),'id'=>'permission_'.$key]) }}
                                                                    {{ Form::label('permission_'.$key, __('Create'),['class'=>'form-check-labe font-weight-500']) }}
                                                                </div>
                                                            @endif
                                                            @if(in_array('Request '.$module,$permissions))
                                                                @php($key = array_search('Request '.$module, $permissions))
                                                                <div class="col-3 form-check">
                                                                    {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_'.str_replace(' ', '', $module),'id'=>'permission_'.$key]) }}
                                                                    {{ Form::label('permission_'.$key, __('Request'),['class'=>'form-check-labe font-weight-500']) }}
                                                                </div>
                                                            @endif
                                                            @if(in_array('Edit '.$module,$permissions))
                                                                @php($key = array_search('Edit '.$module, $permissions))
                                                                <div class="col-3 form-check">
                                                                    {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_'.str_replace(' ', '', $module),'id'=>'permission_'.$key]) }}
                                                                    {{ Form::label('permission_'.$key, __('Edit'),['class'=>'form-check-labe font-weight-500']) }}
                                                                </div>
                                                            @endif
                                                            @if(in_array('Delete '.$module,$permissions))
                                                                @php($key = array_search('Delete '.$module, $permissions))
                                                                <div class="col-3 form-check">
                                                                    {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_'.str_replace(' ', '', $module),'id'=>'permission_'.$key]) }}
                                                                    {{ Form::label('permission_'.$key, __('Delete'),['class'=>'form-check-labe font-weight-500']) }}
                                                                </div>
                                                            @endif
                                                            @if(in_array('View '.$module,$permissions))
                                                                @php($key = array_search('View '.$module, $permissions))
                                                                <div class="col-3 form-check">
                                                                    {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_'.str_replace(' ', '', $module),'id'=>'permission_'.$key]) }}
                                                                    {{ Form::label('permission_'.$key, __('View'),['class'=>'form-check-labe font-weight-500']) }}
                                                                </div>
                                                            @endif
                                                            @if(in_array('Move '.$module,$permissions))
                                                                @php($key = array_search('Move '.$module, $permissions))
                                                                <div class="col-3 form-check">
                                                                    {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_'.str_replace(' ', '', $module),'id'=>'permission_'.$key]) }}
                                                                    {{ Form::label('permission_'.$key, __('Move'),['class'=>'form-check-labe font-weight-500']) }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td width="10%">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="align-middle ischeck form-check-input" name="checkall" data-id="other" >
                                                    </div>
                                                    
                                                </td>
                                                    <td width="10%"><label class="ischeck" data-id="other">{{__('Other')}}</label></td>
                                                <td>
                                                    <div class="row">
                                                        @if(in_array('Manage Invoice Payments',$permissions))
                                                            @php($key = array_search('Manage Invoice Payments', $permissions))
                                                            <div class="col-6 form-check">
                                                                {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_other','id'=>'permission_'.$key]) }}
                                                                {{ Form::label('permission_'.$key, __('Manage Invoice Payments'),['class'=>'form-check-labe font-weight-500']) }}
                                                            </div>
                                                        @endif
                                                        @if(in_array('Create Invoice Payment',$permissions))
                                                            @php($key = array_search('Create Invoice Payment', $permissions))
                                                            <div class="col-6 form-check">
                                                                {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_other','id'=>'permission_'.$key]) }}
                                                                {{ Form::label('permission_'.$key, __('Create Invoice Payment'),['class'=>'form-check-labe font-weight-500']) }}
                                                            </div>
                                                        @endif
                                                        @if(in_array('Invoice Add Product',$permissions))
                                                            @php($key = array_search('Invoice Add Product', $permissions))
                                                            <div class="col-6 form-check">
                                                                {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_other','id'=>'permission_'.$key]) }}
                                                                {{ Form::label('permission_'.$key, __('Invoice Add Product'),['class'=>'form-check-labe font-weight-500']) }}
                                                            </div>
                                                        @endif
                                                        @if(in_array('Invoice Edit Product',$permissions))
                                                            @php($key = array_search('Invoice Edit Product', $permissions))
                                                            <div class="col-6 form-check">
                                                                {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_other','id'=>'permission_'.$key]) }}
                                                                {{ Form::label('permission_'.$key, __('Invoice Edit Product'),['class'=>'form-check-labe font-weight-500']) }}
                                                            </div>
                                                        @endif
                                                        @if(in_array('Invoice Delete Product',$permissions))
                                                            @php($key = array_search('Invoice Delete Product', $permissions))
                                                            <div class="col-6 form-check">
                                                                {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_other','id'=>'permission_'.$key]) }}
                                                                {{ Form::label('permission_'.$key, __('Invoice Delete Product'),['class'=>'form-check-labe font-weight-500']) }}
                                                            </div>
                                                        @endif

                                                        @if(in_array('Estimation Add Product',$permissions))
                                                            @php($key = array_search('Estimation Add Product', $permissions))
                                                            <div class="col-6 form-check">
                                                                {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_other','id'=>'permission_'.$key]) }}
                                                                {{ Form::label('permission_'.$key, __('Estimation Add Product'),['class'=>'form-check-labe font-weight-500']) }}
                                                            </div>
                                                        @endif
                                                        @if(in_array('Estimation Edit Product',$permissions))
                                                            @php($key = array_search('Estimation Edit Product', $permissions))
                                                            <div class="col-6 form-check">
                                                                {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input  isscheck isscheck_other','id'=>'permission_'.$key]) }}
                                                                {{ Form::label('permission_'.$key, __('Estimation Edit Product'),['class'=>'form-check-labe font-weight-500']) }}
                                                            </div>
                                                        @endif
                                                        @if(in_array('Estimation Delete Product',$permissions))
                                                            @php($key = array_search('Estimation Delete Product', $permissions))
                                                            <div class="col-6 form-check">
                                                                {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_other','id'=>'permission_'.$key]) }}
                                                                {{ Form::label('permission_'.$key, __('Estimation Delete Product'),['class'=>'form-check-labe font-weight-500']) }}
                                                            </div>
                                                        @endif

                                                        @if(in_array('MDF Add Expense',$permissions))
                                                            @php($key = array_search('MDF Add Expense', $permissions))
                                                            <div class="col-6 form-check">
                                                                {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_other','id'=>'permission_'.$key]) }}
                                                                {{ Form::label('permission_'.$key, __('MDF Add Expense'),['class'=>'form-check-labe font-weight-500']) }}
                                                            </div>
                                                        @endif
                                                        @if(in_array('MDF Edit Expense',$permissions))
                                                            @php($key = array_search('MDF Edit Expense', $permissions))
                                                            <div class="col-6 form-check">
                                                                {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_other','id'=>'permission_'.$key]) }}
                                                                {{ Form::label('permission_'.$key, __('MDF Edit Expense'),['class'=>'form-check-labe font-weight-500']) }}
                                                            </div>
                                                        @endif
                                                        @if(in_array('MDF Delete Expense',$permissions))
                                                            @php($key = array_search('MDF Delete Expense', $permissions))
                                                            <div class="col-6 form-check">
                                                                {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_other','id'=>'permission_'.$key]) }}
                                                                {{ Form::label('permission_'.$key, __('MDF Delete Expense'),['class'=>'form-check-labe font-weight-500']) }}
                                                            </div>
                                                        @endif

                                                        @if(in_array('Manage Email Templates',$permissions))
                                                            @php($key = array_search('Manage Email Templates', $permissions))
                                                            <div class="col-6 form-check">
                                                                {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_other','id'=>'permission_'.$key]) }}
                                                                {{ Form::label('permission_'.$key, __('Manage Email Templates'),['class'=>'form-check-labe font-weight-500']) }}
                                                            </div>
                                                        @endif
                                                        @if(in_array('Edit Email Template',$permissions))
                                                            @php($key = array_search('Edit Email Template', $permissions))
                                                            <div class="col-6 form-check">
                                                                {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_other','id'=>'permission_'.$key]) }}
                                                                {{ Form::label('permission_'.$key, __('Edit Email Template'),['class'=>'form-check-labe font-weight-500']) }}
                                                            </div>
                                                        @endif
                                                        @if(in_array('On-Off Email Template',$permissions))
                                                            @php($key = array_search('On-Off Email Template', $permissions))
                                                            <div class="col-6 form-check">
                                                                {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_other','id'=>'permission_'.$key]) }}
                                                                {{ Form::label('permission_'.$key, __('On-Off Email Template'),['class'=>'form-check-labe font-weight-500']) }}
                                                            </div>
                                                        @endif
                                                        @if(in_array('Edit Email Template Lang',$permissions))
                                                            @php($key = array_search('Edit Email Template Lang', $permissions))
                                                            <div class="col-6 form-check">
                                                                {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_other','id'=>'permission_'.$key]) }}
                                                                {{ Form::label('permission_'.$key, __('Edit Email Template Lang'),['class'=>'form-check-labe font-weight-500']) }}
                                                            </div>
                                                        @endif

                                                        @if(in_array('Buy Plan',$permissions))
                                                            @php($key = array_search('Buy Plan', $permissions))
                                                            <div class="col-6 form-check">
                                                                {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_other','id'=>'permission_'.$key]) }}
                                                                {{ Form::label('permission_'.$key, __('Buy Plan'),['class'=>'form-check-labe font-weight-500']) }}
                                                            </div>
                                                        @endif
                                                        @if(in_array('View Order',$permissions))
                                                            @php($key = array_search('View Order', $permissions))
                                                            <div class="col-6 form-check">
                                                                {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_other','id'=>'permission_'.$key]) }}
                                                                {{ Form::label('permission_'.$key, __('View Order'),['class'=>'form-check-labe font-weight-500']) }}
                                                            </div>
                                                        @endif
                                                        @if(in_array('Convert Lead To Deal',$permissions))
                                                            @php($key = array_search('Convert Lead To Deal', $permissions))
                                                            <div class="col-6 form-check">
                                                                {{ Form::checkbox('permissions[]',$key,false,['class' => 'form-check-input isscheck isscheck_other','id'=>'permission_'.$key]) }}
                                                                {{ Form::label('permission_'.$key, __('Convert Lead To Deal'),['class'=>'form-check-labe font-weight-500']) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
    <button type="submit" class="btn  btn-primary">{{__('Create')}}</button>
</div>
{{ Form::close() }}
<script>
    $(document).ready(function () {
           $("#checkall").click(function(){
                $('input:checkbox').not(this).prop('checked', this.checked);
            });      
           $(".ischeck").click(function(){
                var ischeck = $(this).data('id');         
                $('.isscheck_'+ ischeck).prop('checked', this.checked);
            });           
        });
</script>