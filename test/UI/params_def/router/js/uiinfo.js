function adjust_ui()
{
	if(uiinfo.priv == "0")
	{
		var save_button = document.getElementById("save-button");
		var cancel_button = document.getElementById("cancel-button");
		var upgrade_button = document.getElementById("afu-upgrade-button");
		var f_radio_mode = document.getElementById("_f_radio_mode");
		var restore_button = document.getElementById("restore-button");
		var reset_button = document.getElementById("reset-button");
		if(reset_button)
		{
			reset_button.style.display = "none";
		}
		if(restore_button)
		{
			restore_button.style.display = "none";
		}
		if(f_radio_mode)
		{
			f_radio_mode.style.display = "none";
		}
		if(save_button)
		{
			save_button.style.display = "none";
		}
		if(cancel_button)
		{
			cancel_button.style.display = "none";
		}
		if(upgrade_button)
		{
			upgrade_button.style.display = "none";
		}
	}
}
adjust_ui();
