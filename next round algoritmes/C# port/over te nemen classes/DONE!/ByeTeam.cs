using Shared.Domain;

namespace Shared.Algorithms
{
	/// <summary>
	/// Een bye team
	/// Een team dat in een ronde de bye krijgt, "speelt" tegen dit team
	/// </summary>
	class ByeTeam : Team
	{
		/// <summary>
		/// Default constructor
		/// </summary>
		public ByeTeam() 
		{
			//Byeteams hebben een ID van -1 zodat ze uit de resultaten te halen zijn.
			TeamID = -1;
		}
	}
}
