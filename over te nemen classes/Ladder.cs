using System;
using System.Collections.Generic;
using System.Collections;
using Shared.Domain;
using Shared.Datastructures;

namespace Shared.Algorithms
{

	/// <summary>
	/// De klasse die verantwoordelijk is voor het draaien van het ladderalgoritme
	/// dat voor elke ronde wedstrijden genereert volgens het zwitserse ladder systeem
	/// </summary>
	internal class Ladder
	{
		/// <summary>
		/// De beste tot nu toe gevonden matching
		/// </summary>
		List<Match> bestMatching;
		/// <summary>
		/// De penalty die hoort bij de beste tot nu toe gevonden matching
		/// </summary>
		int minPenalty;

		/// <summary>
		/// De ronde waar in gespeeld wordt
		/// </summary>
		int round;
		/// <summary>
		/// Alle teams
		/// </summary>
		List<Team> teams;
		/// <summary>
		/// De index vanaf waar de inactieve teams in de teams lijst staan
		/// </summary>
		int inactiveindex;

		/// <summary>
		/// Negeer de spelergraaf als er toch een deadlock optreedt
		/// </summary>
		bool ignoreGraph = false;

		/// <summary>
		/// Genereert, gegeven een lijst van teams en een ronde nummer, de best mogelijke combinatie van wedstrijden
		/// Hierbij wordt gelet op de afstand op de ladder tussen de teams en er wordt een mogelijke "deadlock" in
		/// de volgende ronde voorkomen.
		/// </summary>
		/// <param name="teams">De lijst van teams die tegen elkaar spelen</param>
		/// <param name="possibilities"> De graaf van mogelijke wedstrijden </param>
		/// <param name="round">Het ronde nummer</param>
		/// <param name="inactiveindex"> </param>
		/// <returns>De best mogelijke lijst van wedstrijden voor deze ronde</returns>
		internal List<Match> GenerateLadder(List<Team> teams, Graph possibilities, int round, int inactiveindex)
		{

			//initialiseer variabelen
			ignoreGraph = false;
			bestMatching = null;
			minPenalty = int.MaxValue;
			this.round = round;
			this.teams = teams;
			List<Match> generatedMatches = new List<Match>(teams.Count / 2);
			BitArray matchedup = new BitArray(teams.Count);
			matchedup[0] = true;
			this.inactiveindex = inactiveindex;


			//begin met het matchen van teams
			for (int i = 0; i < matchedup.Count; i++)
			{
				if (!matchedup[i] && possibilities[0, i])
					recurseMatch(0, matchedup, possibilities, generatedMatches, 0, 0, i, true);
			}

			if (bestMatching == null)
			{
				//Logger.Write("Algoritmes", string.Format("Ladderalgoritme heeft geen correcte uitvoer in ronde {0} met {1} teams en graaf {2}.", round, teams.Count, possibilities.ToQuickSave()));
				ignoreGraph = true;
				for (int i = 0; i < matchedup.Count; i++)
				{
					if (!matchedup[i] && possibilities[0, i])
						recurseMatch(0, matchedup, possibilities, generatedMatches, 0, 0, i, true);
					else if (!matchedup[i])
						recurseMatch(0, matchedup, possibilities, generatedMatches, 0, 0, i, false);
				}
			}

			return bestMatching;
		}

		/// <summary>
		/// Zoekt recursief naar de best mogelijke combinatie van wedstrijden
		/// </summary>
		/// <param name="penalty">De penalty tot nu toe.</param>
		/// <param name="matchedUp">Een bitarray die aangeeft welke teams ingedeeld zijn.</param>
		/// <param name="possibilities">De graaf met mogelijke wedstrijden.</param>
		/// <param name="generatedMatches">De lijst met gemaakte wedstrijden tot nu toe.</param>
		/// <param name="depth">De diepte van de graaf.</param>
		/// <param name="first">Het eerste team dat aan een tegenstander gekoppeld wordt</param>
		/// <param name="matchWith"> Het team waar het eerste team mee gekoppeld wordt </param>
		/// <param name="legalchoice"> Is dit een geldige matching</param>
		private void recurseMatch(int penalty, BitArray matchedUp, Graph possibilities, List<Match> generatedMatches, int depth, int first, int matchWith, bool legalchoice)
		{
			//Return als er geen betere score mogelijk is.
			if (legalchoice)
				penalty += evaluatePenalty(first, matchWith);
			//Als het geen geldige matching is, geef hem dan een extra hoge penalty
			else
				penalty += (int)Math.Pow(evaluatePenalty(first, matchWith), 2);
			
			if (penalty >= minPenalty) return;

			//Genereer een nieuwe wedstrijd.
			generatedMatches.Add(new Match(teams[first], teams[matchWith], "aangemaakt"));
			possibilities.RemoveUndirectedEdge(first, matchWith);
			matchedUp[matchWith] = true;

			//Onderste knoop van de boom.
			//Alle teams zijn gekoppeld aan een tegenstander
			if (depth == (teams.Count - 1) / 2)
			{
				//Kijk naar deadlock situaties
				if (round < teams.Count - 2)
				{
					//Deze indeling is niet geldig vanwege deadlock
					//if(unevenGraph(teams, generatedMatches)) return;
					//ik heb 8 knopen, verdeeld in twee ielandjes van 3 en 5 knopen
					if (possibilities.HasUnevenSubgraph())
					{
						if (minPenalty == int.MaxValue) bestMatching = new List<Match>(generatedMatches);
						matchedUp[matchWith] = false;
						generatedMatches.RemoveAt(generatedMatches.Count - 1);
						possibilities.AddUndirectedEdge(first, matchWith);
						return;
					}

					//Deze indeling leidt onvermijdelijk tot een deadlock in de volgende ronde
					if (possibilities.LeadsToUnevenSubgraph())
					{
						matchedUp[matchWith] = false;
						generatedMatches.RemoveAt(generatedMatches.Count - 1);
						possibilities.AddUndirectedEdge(first, matchWith);
						return;
					}

					//Deze indeling leidt onvermijdelijk tot een deadlock over 2 ronden
					if (round > teams.Count - 3 && possibilities.LeadsToSingleConnectionGraph())
					{
						matchedUp[matchWith] = false;
						generatedMatches.RemoveAt(generatedMatches.Count - 1);
						possibilities.AddUndirectedEdge(first, matchWith);
						return;
					}
				}

				//Als deze score beter is, update dan de beste score
				if (penalty < minPenalty)
				{
					minPenalty = penalty;
					bestMatching = new List<Match>(generatedMatches);
				}
			}
			//nieuwe recursiestap
			else
			{
				//Kies de eerste nog niet gekoppelde speler als speler 1 om een nieuwe match te maken
				int newFirst = findFirstUnmatched(matchedUp, first);
				matchedUp[newFirst] = true;

				//probeer hem aan alle nog niet gekoppelde spelers na hem te koppelen
				for (int i = newFirst + 1; i < matchedUp.Count; i++)
				{
					//Als deze 2 spelers nog niet tegen elkaar gespeeld hebben probeer ze dan te koppelen
					if (!matchedUp[i] && possibilities[newFirst, i])
						recurseMatch(penalty, matchedUp, possibilities, generatedMatches, depth + 1, newFirst, i, true);
					else if (!matchedUp[i] && (ignoreGraph || possibilities[newFirst, i]))
						recurseMatch(penalty, matchedUp, possibilities, generatedMatches, depth + 1, newFirst, i, false);
				}

				//Data terugzeten in originele toestand.
				matchedUp[newFirst] = false;
			}

			//Zet data weer terug in de toestand voordat de methode werd aangeroepen
			matchedUp[matchWith] = false;
			generatedMatches.RemoveAt(generatedMatches.Count - 1);

			if (legalchoice)
				possibilities.AddUndirectedEdge(first, matchWith);
		}

		/// <summary>
		/// Bepaalt de penalty als deze 2 teams tegen elkaar spelen
		/// Hierbij wordt de afstand tussen de 2 teams in de ladder gebruikt, en wegen penalties met
		/// hogere spelers zwaarder
		/// </summary>
		/// <param name="index1">De index op de ladder van team 1</param>
		/// <param name="index2">De index op de ladder van team 2</param>
		/// <returns>De penalty die hoort bij een wedstrijd tussen deze 2 teams</returns>
		private int evaluatePenalty(int index1, int index2)
		{
			//Als de wedstrijd tussen een bye-team en een non-actief team is
			//is de penalty 0
			if (index1 >= inactiveindex)
			{
				//Console.WriteLine("Penalty tussen bye en nonactief op 0 gezet");
				return 0;
			}
			if (index2 >= inactiveindex)
				return 8 * (index1 - index2) * (index1 - index2) * 256 / (index1 + 1);
			return (index1 - index2) * (index1 - index2) * 256 / (index1 + 1);
		}

		/// <summary>
		/// Vind de eerste false index in een gegeven bitarray.
		/// </summary>
		/// <param name="matchup">The BitArray om in te zoeken.</param>
		/// <param name="start"> De index vanaf waar gezocht wordt</param>
		/// <returns>De integer index.</returns>
		private static int findFirstUnmatched(BitArray matchup, int start)
		{
			for (int i = start; i < matchup.Count; i++)
			{
				if (!matchup[i]) return i;
			}

			//Logger.Write("Algoritmes", string.Format("findfirstUnMatched vind geen unmatched in bitarray {0} met start {1}", matchup.ToHexString() , start));
			return -1;
		}
	}
}