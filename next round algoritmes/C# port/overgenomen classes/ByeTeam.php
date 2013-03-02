<?php
/// <summary>
/// Een bye team
/// Een team dat in een ronde de bye krijgt, "speelt" tegen dit team
/// </summary>
class ByeTeam extends Team
{
	/// <summary>
	/// Default constructor
	/// </summary>
	public function ByeTeam() 
	{
		//Byeteams hebben een ID van -1 zodat ze uit de resultaten te halen zijn.
		$iTeamID = -1;
	}
}