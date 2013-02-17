using System;

namespace Shared.Algorithms
{
    
    /// <summary>
    /// Het RoundRobin object is een roundrobin counter die loopt tussen 
    /// een gegeven start en eindwaarde
    /// </summary>
    public class RoundRobin
    {
        //de interne counter die van de range van start naar eind afloopt
        private int roundRobinCounter;

        //de ondergrens van de RoundRobin counter
        private int start;

        //de bovengrens van de RoundRobin counter
        private int end;

        /// <summary>
        /// Maak een RoundRobin object dat telt tussen start en end inclusief
        /// </summary>
        /// <param name="start"> startgetal </param>
        /// <param name="end"> eindgetal </param>
        public RoundRobin(int start, int end)
        {
            this.start = Math.Min(start, end);
            this.end = Math.Max(start, end);
        }

        /// <summary>
        /// Property voor de waarde van de conuter
        /// </summary>
        public int Value
        {
            get { return start + roundRobinCounter; }
        }

        /// <summary>
        /// verhoog de roundrobin counter zodat het op het volgende getal staat
        /// </summary>
        /// <returns> De nieuwe waarde van de counter</returns>
        public int Next()
        {
            //roundrobin loopt van start naar end,
            //dit betekend dus dat we ten aller tijden een modulo van
            //end - start + 1 moeten nemen van de roundrobin counter.
            roundRobinCounter++;
            roundRobinCounter = roundRobinCounter % (end - start + 1);

            return Value;
        }

        /// <summary>
        /// verlaag de bovengrens van de counter met 1
        /// </summary>
        public void DecreaseMax()
        {
            //Kijk of de bovengrens niet onder de ondergrens duikt
            if (start == end)
            {
                //we willen geen grensoverschreiding
                return;
            }

            //Verlaag de bovengrens en controleer of de counter goed zit
            end--;
            roundRobinCounter = roundRobinCounter % (end - start + 1);
        }

        /// <summary>
        /// verhoog de bovengrens van de counter met 1
        /// </summary>
        public void IncreaseMax()
        {
            //Verhoog de bovengrens
            end++;
        }

        /// <summary>
        /// Property voor de bovengrens van de counter
        /// </summary>
        public int End
        {
            set 
            {
                if (value < start)
                {
                    //grens overschreden
                    value = start;
                }

                //Verander de bovengrens en controleer of de counter goed zit.
                end = value;
                roundRobinCounter = roundRobinCounter % (end - start + 1); 
            }
        }

        /// <summary>
        /// Property voor de ondergrens van de counter
        /// </summary>
        public int Start
        {
            set
            {
                if (value > end)
                {
                    //grens is overschreden
                    value = end;
                }

                start = value;
                roundRobinCounter = roundRobinCounter % (end - start + 1);
            }
        }
    }

}
