$("#new_trial_type_button").on("click",function(){
  var new_trial_type_name = prompt("What do you want to call your new trial type?");
  
  create_trial_type(new_trial_type_name);
  
});

$("#trial_type_data").on("input", "textarea", function() {
    $(this).addClass("modified");
});

$("#trial_type_select, #trial_type_file_select").on("change", function() {
    save_trial_types();
    
    var trial_type = $("#trial_type_select").val();
    var file       = $("#trial_type_file_select").val();
    
    show_trial_type(trial_type,file);
    
    
    var current_trialtype_template=$("#"+trial_type+"template_textarea").val();
    
    trialtype_to_canvas(current_trialtype_template);
        
});

$("#save_btn").on("click", save_trial_types);

$("#run_button").on("click", function() {
        
    simulate_experiment();
});