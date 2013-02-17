//using System;
//using System.Collections.Generic;
//using System.Collections;
//using Shared.Logging;

//namespace Shared.Datastructures
//{
//    public class CastList<T>
//    {
//        public static List<T> Cast(IEnumerable list)
//        {
//            List<T> ret = new List<T>();

//            foreach (var item in list)
//            {
//                try
//                {
//                    ret.Add((T)item);
//                }
//                //TODO iets zinnigs in de catch zetten
//                catch (Exception e)
//                {
//                    Logger.Write("CastList.Cast", e.ToString());
//                }
//            }

//            return ret;
//        }
//    }
//}
