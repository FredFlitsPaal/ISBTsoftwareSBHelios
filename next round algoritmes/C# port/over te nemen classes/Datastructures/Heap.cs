using System;
using System.Collections.Generic;
using System.Collections;

namespace Shared.Datastructures
{

    //TODO: Commentaar toevoegen aan methoden

	/// <summary>
	/// Een implementatie van een heap
	/// </summary>
	/// <typeparam name="T"></typeparam>
    public class Heap<T> : IList<T> where T : IComparable<T>
    {
		/// <summary>
		/// lijst representatie van de heap
		/// </summary>
        List<T> data;

		/// <summary>
		/// Default constructor voor een lege heap
		/// </summary>
        public Heap()
        {
            data = new List<T>();
        }

		/// <summary>
		/// Default constructor voor een lege heap met een capaciteit
		/// </summary>
		/// <param name="capacity"> Capaciteit van de heap</param>
        public Heap(int capacity)
        {
            data = new List<T>(capacity);
        }

		/// <summary>
		/// Constructor die een heap maakt met een collection
		/// </summary>
		/// <param name="collection"></param>
		public Heap(IEnumerable<T> collection)
		{
			data = new List<T>(collection);
			for (int i = 1 + data.Count / 2; i >= 0; i--)
				siftDown(i);
		}

		/// <summary>
		/// Het aantal elementen in de heap
		/// </summary>
        public int Count
        {
            get { return data.Count; }
        }


        #region standard methods

		/// <summary>
		/// Maak de heap leag
		/// </summary>
        public void Clear()
        {
            data.Clear();
        }

        /// <summary>
        /// Vraag een enumerator voor de heap op
        /// </summary>
        /// <returns></returns>
        public IEnumerator<T> GetEnumerator()
        {
            return data.GetEnumerator();
        }

        #endregion

		/// <summary>
		/// Bevat de heap een element
		/// </summary>
		/// <param name="item"> het element waarvoor gezocht wordt</param>
		/// <returns></returns>
        public bool Contains(T item)
        {
            return Contains(item, 0);
        }

		/// <summary>
		/// Doet een geoptimaliseerde search naar item vanaf index
		/// </summary>
		/// <param name="item"> het element waarvoor gezocht wordt </param>
		/// <param name="index"> de startindex </param>
		/// <returns></returns>
        bool Contains(T item, int index)
        {
            int c = data[index].CompareTo(item);

            if (c > 0)
                return false;

            if (c == 0)
                return true;

            return Contains(item, (index << 1) + 1) || Contains(item, (index << 1) + 2);
        }

		/// <summary>
		/// Bevat de heap een element die aan het predicaat voldoet
		/// </summary>
		/// <param name="match"> Het predikaat waaraan voldaan moet worden</param>
		/// <returns></returns>
        public bool Contains(Predicate<T> match)
        {
            foreach (T item in data)
            {
                if (match(item))
                    return true;
            }

            return false;
        }

		/// <summary>
		/// Kijkt of de heap een element bevat dat aan het predikaat voldoet
		/// en, als dit het geval is, dat element in result zet
		/// </summary>
		/// <param name="match"> Het predikaat waaraan voldaan moet worden </param>
		/// <param name="result"> Het element </param>
		/// <returns> </returns>
        public bool TryFind(Predicate<T> match, out T result)
        {
            foreach (T item in data)
            {
                if (match(item))
                {
                    result = item;
                    return true;
                }
            }

            result = default(T);
            return false;
        }

		/// <summary>
		/// Herorden de heap
		/// </summary>
		/// <param name="index"></param>
        public void ReSort(int index)
        {
            siftUp(index);
        }

		/// <summary>
		/// Haal het kleinste element uit de heap
		/// </summary>
		/// <returns> Het kleinste element in de heap </returns>
        public T RemoveMin()
        {
            T min = data[0];
            int index = data.Count - 1;
            data[0] = data[index];
            data.RemoveAt(index);
            siftDown(0);
            return min;
        }

		/// <summary>
		/// Doet een heap siftdown operatie
		/// </summary>
		/// <param name="index"></param>
        void siftDown(int index)
        {
            int child0 = (index << 1) + 1;

            if (child0 >= data.Count)
                return;

            int child1 = child0 + 1;
            int c0 = data[index].CompareTo(data[child0]);

            if (child1 >= data.Count)
            {
                if (c0 > 0)
                    swap(index, child0);
            }
            else
            {
                if (c0 > 0 || data[index].CompareTo(data[child1]) > 0)
                {
                    int cc = data[child0].CompareTo(data[child1]);
                    if (cc > 0)
                    {
                        swap(index, child1);
                        siftDown(child1);
                    }
                    else
                    {
                        swap(index, child0);
                        siftDown(child0);
                    }
                }
            }
        }

		/// <summary>
		/// Swapt 2 elementen in de heap
		/// </summary>
		/// <param name="i0"> element 1 </param>
		/// <param name="i1"> element 2</param>
        void swap(int i0, int i1)
        {
            T temp = data[i0];
            data[i0] = data[i1];
            data[i1] = temp;
        }

		/// <summary>
		/// Voegt een elment toe aan de heap
		/// </summary>
		/// <param name="item"> het toe te voegen element</param>
        public void Add(T item)
        {
            data.Add(item);

            if (data.Count == 1)
                return;

            siftUp(data.Count - 1);
            return;
        }

		/// <summary>
		/// Doet een siftup operatie
		/// </summary>
		/// <param name="index"></param>
        void siftUp(int index)
        {
            int index2 = (index - 1) >> 1;
            while (data[index].CompareTo(data[index2]) < 0)
            {
                swap(index2, index);
                index = index2;
                index2 = (index - 1) >> 1;
                if (index2 < 0)
                    return;
            }
        }

        #region IEnumerable Members

		/// <summary>
		/// Vraag een enumerator voor de heap op
		/// </summary>
		/// <returns> Een enumerator voor de heap</returns>
        IEnumerator IEnumerable.GetEnumerator()
        {
            return GetEnumerator();
        }

        #endregion

        #region ICollection<T> Members

		/// <summary>
		/// Kopieert een array in de heap vanaf een index
		/// </summary>
		/// <param name="array"> Het array dat gekopieerd wordt </param>
		/// <param name="arrayIndex"> De index waar het array naar gekopieerd wordt</param>
        public void CopyTo(T[] array, int arrayIndex)
        {
            for (int i = 0; i < data.Count && i < array.Length; i++)
            {
                array[i + arrayIndex] = data[i];
            }
        }

		/// <summary>
		/// Een heap is niet read-only
		/// </summary>
        public bool IsReadOnly
        {
            get { return false; }
        }

		/// <summary>
		/// Verwijdert een element uit de heap.
		/// Returnt false als het element niet in de heap is
		/// </summary>
		/// <param name="item"> Het te verwijderen element </param>
		/// <returns></returns>
        public bool Remove(T item)
        {
            int index = data.IndexOf(item);
            if (index < 0)
                return false;
            data[index] = data[data.Count - 1];
            data.RemoveAt(data.Count - 1);
            siftDown(index);
            return true;
        }

        #endregion

		/// <summary>
		/// Verwijdert een element uit de heap op een index
		/// </summary>
		/// <param name="index"> De index die verwijderd wordt</param>
        public void RemoveAt(int index)
        {
            data[index] = data[data.Count - 1];
            data.RemoveAt(data.Count - 1);
            siftDown(index);
        }

		/// <summary>
		/// Vraagt de index op van een element in de heap
		/// </summary>
		/// <param name="item"> Het element </param>
		/// <returns> de index van het element</returns>
        public int IndexOf(T item)
        {
            return IndexOf(item, 0);
        }

		/// <summary>
		/// Geoptimaliseerde search naar de index van een element
		/// </summary>
		/// <param name="item"> Het element </param>
		/// <param name="index"> zoekindex </param>
		/// <returns> De index van het element </returns>
        int IndexOf(T item, int index)
        {
            int c = data[index].CompareTo(item);

            if (c > 0)
                return -1;

            if (c == 0)
                return index;

            int i1 = IndexOf(item, (index << 1) + 1);

            if (i1 >= 0)
                return i1;

            i1 = IndexOf(item, (index << 1) + 2);

            if (i1 >= 0)
                return i1;

            return -1;
        }

		/// <summary>
		/// Verander de waarde van een element op een index
		/// </summary>
		/// <param name="index"> De index </param>
		/// <param name="newValue"> De nieuwe waarde </param>
        public void ModifyValue(int index, T newValue)
        {
            T old = data[index];
            data[index] = newValue;
            int cmp = old.CompareTo(newValue);
            if (cmp == 0)
                return;
            if (cmp > 0)
                siftUp(index);
            else
                siftDown(index);
        }

        #region IList<T> Members

        /// <summary>
        /// !!WAARSCHUWING!!
        /// Insert het element niet op de gegeven index, maar gedraagt als
        /// een Add operatie
        /// </summary>
        /// <param name="index"> Wordt genegeerd</param>
        /// <param name="item"> Het element dat toegevoegd wordt</param>
        public void Insert(int index, T item)
        {
            Add(item);
        }

		/// <summary>
		/// Het element op een index
		/// </summary>
		/// <param name="index"> De index van het element </param>
		/// <returns> Het element </returns>
        public T this[int index]
        {
            get
            {
                return data[index];
            }
            set
            {
                data[index] = value;
            }
        }

        #endregion

		/// <summary>
		/// Testing
		/// </summary>
        public static void Test()
        {
            Heap<int> testheap = new Heap<int>(new int[] { 7, 6, 5, 4, 3, 2, 1, 0 });
            for (int i = 0; i < 8; i++)
            {
                int value = testheap.RemoveMin();
                if (value != i)
                    throw new Exception("Heap failed");
            }

            for (int i = 0; i < 10; i++)
            {
                testheap.Add(i);
            }
            for (int i = 0; i < 10; i++)
            {
                int value = testheap.RemoveMin();
                if (value != i)
                    throw new Exception("Heap failed");
            }
        }
    }
}
