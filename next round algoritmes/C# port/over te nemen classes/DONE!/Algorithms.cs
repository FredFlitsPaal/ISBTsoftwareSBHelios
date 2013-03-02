using System.Collections.Generic;
using Shared.Domain;
using Shared.Datastructures;
using Shared.Logging;

namespace Shared.Algorithms
{

	/// <summary>
	/// Bevat de methodes voor algoritmes die de rest van het programma kan aanroepen.
	/// Controleert ook de gegeven invoer en sorteert deze voor de algoritmes.
	/// </summary>
	public static class Algorithms
	{

		#region algoritmecalls

		/// <summary>
		/// Koppelt een lijst van spelers aan elkaar, waarbij spelers waar
		/// mogelijk aan spelers van een andere vereniging worden gekoppeld.
		/// Deze methode verandert niets aan de input lijst.
		/// </summary>
		/// <param name="inputPlayers"> 
		/// Een lijst met een even aantal spelers.
		/// </param>
		/// <returns>Een lijst van teams of null als de input ongeldig is.</returns>
		public static List<Team> MatchDoubles(IList<Player> inputPlayers)
		{
			//Controleer input.
			if (!VerifyDoublesPlayers(inputPlayers))
			{
				Logger.Write("Algoritmes", string.Format("Ongeldige teamindelings data op {0} spelers.", inputPlayers.Count));
				return null;
			}

			//Maak nieuwe lijst en sorteer deze.
			List<Player> players = new List<Player>(inputPlayers);
			SortDoublesPlayers(players);

			//Maak de teamindeling.
			Logger.Write("Algoritmes", string.Format("Begin teamindeling op {0} spelers.", inputPlayers.Count));
			return Matcher.TeamDoubles(players);
		}

		/// <summary>
		/// Koppelt een lijst van spelers aan elkaar, waarbij spelers aan spelers van 
		/// het andere geslacht worden gekoppeld.
		/// Waar mogelijk gebeurt dit met een speler van een andere vereniging.
		/// Deze methode verandert niets aan de input lijst.
		/// </summary>
		/// <param name="inputPlayers"> 
		/// Een lijst van spelers waarij het aantal mannen gelijk is aan het aantal vrouwen.
		/// </param>
		/// <returns>Een lijst van teams of null als de input ongeldig is.</returns>
		public static List<Team> MatchMixedDoubles(IList<Player> inputPlayers)
		{
			//Controleer input.
			if (!VerifyMixedDoublesPlayers(inputPlayers))
			{
				Logger.Write("Algoritmes", string.Format("Ongeldige mixed-teamindelings data op {0} spelers.", inputPlayers.Count));
				return null;
			}

			//Maak nieuwe lijst en sorteer deze.
			List<Player> players = new List<Player>(inputPlayers);
			SortMixedDoublesPlayers(players);

			//Maak de teamindeling.
			Logger.Write("Algoritmes", string.Format("Begin mixed-teamindeling op {0} spelers.", inputPlayers.Count));
			return MixedMatcher.TeamDoubles(players);
		}

		/// <summary>
		/// Genereert, gegeven een lijst van teams en een ronde nummer, de best mogelijke combinatie van wedstrijden
		/// Hierbij wordt gelet op de afstand op de ladder tussen de teams, speelt elk team maximaal 1 keer niet en 
		/// speelt elk team maximaal 1 keer tegen elk ander team.
		/// </summary>
		/// <param name="inputTeams">De lijst van teams die tegen elkaar spelen</param>
		/// <param name="round">Het ronde nummer</param>
		/// <param name="inputPlayedgames">De wedstrijden die al gespeeld zijn in de voorgaande rondes</param>
		/// <param name="notplaying">Het team dat niet ingeeld is, of null als deze niet bestaat.</param>
		/// <returns>De best mogelijke lijst van wedstrijden voor deze ronde of null bij een ongeldige invoer.</returns>
		public static List<Match> GenerateLadder(IList<Team> inputTeams, int round, IList<Match> inputPlayedgames, out Team notplaying)
		{
			//Controleer data
			if (!VerifyLadderData(inputTeams, inputPlayedgames, round))
			{
				notplaying = null;
				//Logger.Write("Algoritmes", string.Format("Ongeldige ladder invoer van ronde {0}, met {1} teams en {2} gespeelde wedstrijden.", round, inputTeams.Count, inputPlayedgames.Count));
				return null;
			}

			List<Team> teams = new List<Team>(inputTeams);
			List<Match> playedgames = new List<Match>(inputPlayedgames);

			//Voeg een mogelijk ByeTeam toe.
			bool hasBye = false;

			if ((teams.Count & 1) != 0)
			{
				teams.Add(new ByeTeam());
				hasBye = true;
			}

			//Maak de graaf met mogelijke wedstrijden.
			Graph possibilities = Graph.Create(teams.Count);
			possibilities.Fill();

			foreach (Match playedmatch in playedgames)
			{
				possibilities.RemoveUndirectedEdge(indexOfTeam(teams, playedmatch.TeamA), indexOfTeam(teams, playedmatch.TeamB));
			}

			//Een team kan niet tegen zichzelf spelen.
			for (int i = 0; i < teams.Count - 1; i++)
			{
				possibilities.RemoveUndirectedEdge(i, i);
			}

			//Verwijder alle al gespeelde wedstrijden met byeteams
			if (hasBye)
			{
				foreach (Team team in teams)
				{
					int teamIndex = indexOfTeam(teams, team);
					int playedMatches = 0;

					//Tel het aantal gespeelde matches, inclusief tegen inactieve teams
					foreach (Match match in playedgames)
						if (match.TeamA.TeamID == team.TeamID || match.TeamB.TeamID == team.TeamID)
							playedMatches++;

					if (playedMatches != round - 1)
						possibilities.RemoveUndirectedEdge(teams.Count - 1, teamIndex);

				}
			}
			//Bepaalt vanaf welke index alle volgende teams inactief of bye zijn.
			int inactiveindex = teams.Count;

			for (int i = 0; i < teams.Count; i++)
			{
				if (teams[i].IsInOperative || teams[i].TeamID == -1)
				{
					inactiveindex = i;
					break;
				}
			}

			//Zet alle onderlinge mogelijkheden tussen inactieve en byeteams op true.
			for (int i = inactiveindex; i < teams.Count; i++)
			{
				for (int j = i + 1; j < teams.Count; j++)
				{
					possibilities.AddUndirectedEdge(i, j);
				}
			}

			//Genereer de nieuwe wedstrijdindeling.
			Ladder ladder = new Ladder();
			//Logger.Write("Algoritmes", string.Format("Begin ladder algoritme in ronde {0}, met {1} teams en {2} gespeelde wedstrijden.", round, inputTeams.Count, inputPlayedgames.Count));
			List<Match> result = ladder.GenerateLadder(teams, possibilities, round, inactiveindex);

			//Verwijdert bye team en geeft aan welk team niet speelt.
			notplaying = null;

			if (hasBye)
			{
				Match toRemove = null;

				//Zoek de wedstrijd met het byeteam.
				foreach (Match match in result)
				{
					if (match.TeamA.TeamID == -1 || match.TeamB.TeamID == -1)
					{
						toRemove = match;
						break;
					}
				}
				//Zet het team dat tegen de bye heeft gespeeld.
				if (toRemove.TeamA.TeamID != -1)
				{
					notplaying = toRemove.TeamA;
				}
				else if (toRemove.TeamB.TeamID != -1)
				{
					notplaying = toRemove.TeamB;
				}


				result.Remove(toRemove);
				//Als het andere team actief is, dan telt het als een halve wedstrijd.
				if (notplaying.IsInOperative)
					notplaying = null;
			}

			return result;
		}

		/// <summary>
		/// Bepaald de graad van een knoop in een graaf.
		/// </summary>
		/// <param name="possibilities">De graaf.</param>
		/// <param name="line">De node index om te controleren.</param>
		/// <param name="size">Het aantal nodes in de graaf.</param>
		/// <returns></returns>
		public static int countLine(Graph possibilities, int line, int size)
		{
			int count = 0;

			for (int i = 0; i < size; i++)
			{
				if (possibilities[line, i])
					count++;
			}


			// TODO: return CountEdges(line) als het 100% goed blijkt te zijn
			if (count != possibilities.CountEdges(line))
				Logger.Write("CountEdges failed", "naive count != bithack count");

			return count;
		}

		/// <summary>
		/// Zoekt de index van een team in een lijst.
		/// </summary>
		/// <param name="teams">De IList van teams om in te zoeken.</param>
		/// <param name="teamtocheck">Het team om de index van te zoeken.</param>
		/// <returns>De index van het team, of -1 bij niet gevonden.</returns>
		public static int indexOfTeam(IList<Team> teams, Team teamtocheck)
		{
			for (int i = 0; i < teams.Count; i++)
			{
				if (teams[i].TeamID == teamtocheck.TeamID)
					return i;
			}

			return -1;
		}

		#endregion

		#region verification

		/// <summary>
		/// Controleert of een gegeven lijst van spelers een even grootte heeft.
		/// </summary>
		/// <param name="players">De te controleren lijst met spelers.</param>
		/// <returns>Een boolean die de correctheid van de lijst aangeeft.</returns>
		public static bool VerifyDoublesPlayers(IList<Player> players)
		{
            //Oneven aantal spelers
			return ((players.Count & 1) == 0);
		}

		/// <summary>
		/// Controleert of een gegeven lijst van spelers een even grootte heeft en 
		/// net zoveel mannen als vrouwen bevat..
		/// </summary>
		/// <param name="players">De te controleren lijst met spelers.</param>
		/// <returns>Een boolean die de correctheid van de lijst aangeeft.</returns>
		public static bool VerifyMixedDoublesPlayers(IList<Player> players)
		{
            //Oneven aantal spelers
			if ((players.Count & 1) != 0) return false;

			int male = 0;
			int female = 0;

            //Tel het aantal mannen en vrouwen
            foreach (Player p in players)
            {
                if (p.Gender == "M") male++;
                else female++;
            }

		    return (male == female);
		}

		/// <summary>
		/// Controleert of het rondenummer van de ladder mogelijk is en alle teams uit de wedstrijden
		/// in de lijst van teams zitten.
		/// </summary>
		/// <param name="teams">De teams.</param>
		/// <param name="playedmatches">De gespeelde westrijden.</param>
		/// <param name="round">Het rondenummer.</param>
		/// <returns>Een boolean die de correctheid vand e data aangeeft.</returns>
		public static bool VerifyLadderData(IList<Team> teams, IList<Match> playedmatches, int round)
		{
			//Rond af naar volgende even waarde.
			int teamamounttocheck = teams.Count + (teams.Count & 1);
            //Ongeldig rondenummer
			if (round < 1 || round > teamamounttocheck - 1)
				return false;

            //Er komen teams voor in de gespeelde wedstrijden die niet bestaan
            foreach (Match playedmatch in playedmatches)
            {
                if (indexOfTeam(teams,playedmatch.TeamA) == -1 || indexOfTeam(teams,playedmatch.TeamB) == -1)
                    return false;
            }

		    return true;
		}
		#endregion

		#region sorting

		/// <summary>
		/// Sorteert een lijst van spelers zo dat de spelers van de vereniging met het meeste aantal spelers 
		/// in deze lijst gegroepeerd vooraan staan.
		/// </summary>
		/// <param name="players">De lijst met spelers die gesorteerd moet worden.</param>
		public static void SortDoublesPlayers(List<Player> players)
		{
			Dictionary<string, List<Player>> clubmap = new Dictionary<string, List<Player>>();

			foreach (Player p in players)
			{
				if (!clubmap.ContainsKey(p.Club))
					clubmap.Add(p.Club, new List<Player>());

				clubmap[p.Club].Add(p);
			}

			players.Clear();

			while (clubmap.Count != 0)
			{
			    int max = 0;
			    string key = "";
			    foreach (List<Player> clubplayers in clubmap.Values)
			    {
			        if (clubplayers.Count > max)
			        {
			            max = clubplayers.Count;
			            key = clubplayers[0].Club;
			        }
			    }

			    List<Player> toadd = clubmap[key];
			    clubmap.Remove(key);

                foreach (Player p in toadd)
                {
                    players.Add(p);
                }
			}
		}

		/// <summary>
		/// Sorteert een gegeven lijst van spelers zo dat de mannen vooraan staan in de lijst, en er per geslacht 
		/// de spelers van de vereniging met het meeste aantal spelers bij dat geslacht gegroepeerd vooraan staan.
		/// </summary>
		/// <param name="players">De lijst met spelers die gesorteerd moet worden.</param>
		public static void SortMixedDoublesPlayers(List<Player> players)
		{
			List<Player> males = new List<Player>();
			List<Player> females = new List<Player>();

            //Splits de spelers op in mannen en vrouwen
            foreach (Player p in players)
            {
                if (p.Gender == "M")
                    males.Add(p);
                else females.Add(p);
            }

            //Sorteer mannen en vrouwen afzonderlijk
		    SortDoublesPlayers(males);
			SortDoublesPlayers(females);

            //Voeg mannen en vrouwen samen
			players.Clear();
			players.AddRange(males);
			players.AddRange(females);
		}

		#endregion

	}
}
